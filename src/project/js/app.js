document.addEventListener("DOMContentLoaded", () => {
  const arrow = document.getElementById("toggleMenuArrow");
  const menu = document.getElementById("dropdownMenu");
  const arrowContainer = document.getElementById("toggleMenuArrowContainer");

  // Tooltip iniziale
  new bootstrap.Tooltip(arrowContainer, {
    trigger: "hover",
    html: true,
  });

  const arrowIcon = arrow.querySelector("i");

  // Toggle pannello menu
  arrow.addEventListener("click", () => {
    const tooltipInstance = bootstrap.Tooltip.getInstance(arrowContainer);
    if (tooltipInstance) tooltipInstance.dispose();

    const isOpen = menu.style.top === "0px";

    menu.style.top = isOpen ? "105px" : "0px";
    arrowContainer.style.top = isOpen ? "160px" : "90px";

    arrowIcon.classList.replace(
      isOpen ? "bi-chevron-down" : "bi-chevron-up",
      isOpen ? "bi-chevron-up" : "bi-chevron-down"
    );

    const newTitle = isOpen
      ? "Chiudi il pannello di navigazione"
      : "Apri il pannello di navigazione";
    arrowContainer.setAttribute("title", newTitle);

    new bootstrap.Tooltip(arrowContainer, {
      trigger: "hover",
      html: true,
    });
  });

  // Inizializza il badge al caricamento
  updateCartIcon();

  // Gestione click "Aggiungi al carrello"
  document.querySelectorAll(".add-to-cart").forEach((button) => {
    button.addEventListener("click", () => {
      const id = button.dataset.id;
      const name = button.dataset.name;
      const price = parseFloat(button.dataset.price);

      let cart = JSON.parse(localStorage.getItem("cart")) || {};

      if (cart[id]) {
        cart[id].quantity += 1;
      } else {
        cart[id] = { id, name, price, quantity: 1 };
      }

      localStorage.setItem("cart", JSON.stringify(cart));

      updateCartIcon();

      // Mostra notifica con SweetAlert2
      Swal.fire({
        title: "Aggiunto al carrello",
        text: `${name} è stato aggiunto con successo.`,
        icon: "success",
        showConfirmButton: false,
        timer: 1500,
        toast: true,
        position: "top",
      });
    });
  });
});

function loadCart() {
  const orderList = document.getElementById("orderList");
  const emptyMsg = document.getElementById("emptyMsg");
  const totalAmount = document.getElementById("totalAmount");
  const cart = JSON.parse(localStorage.getItem("cart")) || {};

  orderList.innerHTML = "";
  let total = 0;

  if (Object.keys(cart).length === 0) {
    emptyMsg.classList.remove("d-none");
    orderList.classList.add("d-none");
    totalAmount.textContent = "";
    return;
  }

  emptyMsg.classList.add("d-none");
  orderList.classList.remove("d-none");

  Object.values(cart).forEach((item) => {
    const li = document.createElement("li");
    li.className = "order-list-item";

    const itemInfo = document.createElement("div");
    itemInfo.innerHTML = `<strong>${item.name}</strong> x${item.quantity}`;

    const controls = document.createElement("div");
    controls.classList.add("d-flex", "align-items-center", "gap-2");

    const itemTotal = item.price * item.quantity;
    total += itemTotal;

    const price = document.createElement("span");
    price.className = "badge-price";
    price.textContent = `€${itemTotal.toFixed(2)}`;

    const removeBtn = document.createElement("button");
    removeBtn.className = "btn-remove";
    removeBtn.textContent = "Rimuovi";
    removeBtn.addEventListener("click", () => {
      removeItem(item.id);
    });

    controls.appendChild(price);
    controls.appendChild(removeBtn);

    li.appendChild(itemInfo);
    li.appendChild(controls);
    orderList.appendChild(li);
  });

  totalAmount.textContent = `Totale: €${total.toFixed(2)}`;
}

function updateCartIcon() {
  const cartIconContainer = document.getElementById("cartIconContainer");
  const cartCountBadge = document.getElementById("cartCountBadge");

  const cart = JSON.parse(localStorage.getItem("cart")) || {};
  const totalQuantity = Object.values(cart).reduce(
    (sum, item) => sum + item.quantity,
    0
  );

  if (totalQuantity > 0) {
    cartIconContainer.classList.remove("d-none");
    cartCountBadge.textContent = totalQuantity;

    const cartIcon = cartIconContainer.querySelector('[data-bs-toggle="tooltip"]');
    if (cartIcon && !bootstrap.Tooltip.getInstance(cartIcon)) {
      new bootstrap.Tooltip(cartIcon);
    }
  } else {
    cartIconContainer.classList.add("d-none");
    cartCountBadge.textContent = "0";
  }
}


function removeItem(id) {
  let cart = JSON.parse(localStorage.getItem("cart")) || {};
  delete cart[id];
  localStorage.setItem("cart", JSON.stringify(cart));
  loadCart();
}

function clearCart() {
  localStorage.removeItem("cart");
  updateCartIcon();
  loadCart();
}

document.addEventListener("DOMContentLoaded", () => {
  loadCart();
  document.getElementById("clearCartBtn").addEventListener("click", clearCart);
});

document.addEventListener("DOMContentLoaded", () => {
  const paymentBtn = document.getElementById("paymentBtn");
  const addressDisplay = document.getElementById("deliveryAddress");
  const selectBtn = document.getElementById("selectAddressBtn");
  const changeBtn = document.getElementById("changeAddressBtn");

  const address = localStorage.getItem("indirizzoConsegna");

  if (address) {
    addressDisplay.textContent = address;
    selectBtn.classList.add("d-none");
    changeBtn.classList.remove("d-none");
    paymentBtn.disabled = false;
  } else {
    addressDisplay.textContent = "Nessun indirizzo selezionato";
    selectBtn.classList.remove("d-none");
    changeBtn.classList.add("d-none");
    paymentBtn.disabled = true;
  }

  // Vai a selezionare la posizione
  selectBtn.addEventListener("click", () => {
    window.location.href = "map.php";
  });

  changeBtn.addEventListener("click", () => {
    window.location.href = "map.php";
  });
});

document.addEventListener("DOMContentLoaded", () => {
  const input = document.getElementById("addressInput");
  const suggestionsContainer = document.getElementById("suggestions");
  const searchBtn = document.getElementById("searchBtn");

  // Funzione per mostrare suggerimenti
  function showSuggestions(results) {
    suggestionsContainer.innerHTML = "";
    if (results.length === 0) {
      suggestionsContainer.style.display = "none";
      return;
    }
    results.forEach((place) => {
      const item = document.createElement("button");
      item.type = "button";
      item.className = "list-group-item list-group-item-action";
      item.textContent = place.display_name;
      item.addEventListener("click", () => {
        input.value = place.display_name;
        suggestionsContainer.style.display = "none";
        setDeliveryPoint(parseFloat(place.lat), parseFloat(place.lon));
      });
      suggestionsContainer.appendChild(item);
    });
    suggestionsContainer.style.display = "block";
  }

  // Funzione di ricerca suggerimenti Nominatim (debounce 300ms)
  let debounceTimeout;
  input.addEventListener("input", () => {
    clearTimeout(debounceTimeout);
    const query = input.value.trim();
    if (query.length < 3) {
      suggestionsContainer.style.display = "none";
      return;
    }
    debounceTimeout = setTimeout(() => {
      fetch(
        `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(
          query
        )}&addressdetails=1&limit=5`
      )
        .then((res) => res.json())
        .then((data) => showSuggestions(data))
        .catch(() => (suggestionsContainer.style.display = "none"));
    }, 300);
  });

  // Cerca al click bottone
  searchBtn.addEventListener("click", () => {
    const query = input.value.trim();
    if (!query) {
      alert("Inserisci un indirizzo");
      return;
    }
    // Seleziona il primo suggerimento o geocode manuale
    fetch(
      `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(
        query
      )}&limit=1`
    )
      .then((res) => res.json())
      .then((data) => {
        if (data.length > 0) {
          setDeliveryPoint(parseFloat(data[0].lat), parseFloat(data[0].lon));
          suggestionsContainer.style.display = "none";
        } else {
          alert("Indirizzo non trovato");
        }
      });
  });

  // Nascondi suggerimenti se click fuori
  document.addEventListener("click", (e) => {
    if (!suggestionsContainer.contains(e.target) && e.target !== input) {
      suggestionsContainer.style.display = "none";
    }
  });

  // 1. Inizializza la mappa
  var map = L.map("map").setView([41.35, 13.067], 11.0);

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: "&copy; OpenStreetMap contributors",
  }).addTo(map);

  let sabaudiaBoundary = null;
  let boundaryLayer = null;

  fetch("../assets/geojson/comuni_lazio.geojson")
    .then((res) => res.json())
    .then((data) => {
      const feature = data.features.find(
        (f) => f.properties.name === "Sabaudia"
      );
      if (!feature) {
        console.error("Comune di Sabaudia non trovato nel file!");
        return;
      }
      sabaudiaBoundary = feature;
      boundaryLayer = L.geoJSON(sabaudiaBoundary, {
        style: { color: "blue", weight: 2, fillOpacity: 0 },
      }).addTo(map);
    })
    .catch((err) =>
      console.error("Errore caricamento geojson comuni Lazio:", err)
    );

  // Variabile per il marker della consegna
  var deliveryMarker = null;

  // Funzione per controllo punto-in-poligono
  function checkDeliveryPoint(lat, lon) {
    var pt = turf.point([lon, lat]);
    var inside = turf.booleanPointInPolygon(pt, sabaudiaBoundary);
    return inside;
  }
  function setDeliveryPoint(lat, lon) {
    if (deliveryMarker) {
      map.removeLayer(deliveryMarker);
    }
    deliveryMarker = L.marker([lat, lon], { draggable: true }).addTo(map);
    map.setView([lat, lon], 15);

    // Reverse geocoding per ottenere l’indirizzo
    fetch(
      `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lon}`
    )
      .then((res) => res.json())
      .then((data) => {
        const address = data.display_name || "Indirizzo non disponibile";

        Swal.fire({
          title: "Conferma indirizzo",
          text: `È questo l'indirizzo corretto?\n${address}`,
          icon: "question",
          showCancelButton: true,
          confirmButtonText: "Sì, confermo",
          cancelButtonText: "No, cambia",
        }).then((result) => {
          if (result.isConfirmed) {
            // Salva indirizzo in localStorage
            localStorage.setItem("indirizzoConsegna", address);
            // Vai alla pagina del carrello
            window.location.href = "carrello.php";
          }
        });
      });

    // Se il marker viene trascinato, aggiorna tutto
    deliveryMarker.on("dragend", function (e) {
      const newPos = e.target.getLatLng();
      setDeliveryPoint(newPos.lat, newPos.lng); // richiama la stessa funzione
    });
  }

  // Funzione per geocoding con Nominatim
  function geocode(address) {
    fetch(
      `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(
        address
      )}`
    )
      .then((response) => response.json())
      .then((data) => {
        if (data.length > 0) {
          let lat = parseFloat(data[0].lat);
          let lon = parseFloat(data[0].lon);
          setDeliveryPoint(lat, lon);
        } else {
          alert("Indirizzo non trovato");
        }
      })
      .catch(() => alert("Errore nella ricerca"));
  }

  // Eventi ricerca
  document.getElementById("searchBtn").addEventListener("click", () => {
    const address = document.getElementById("addressInput").value.trim();
    if (address) {
      geocode(address);
    } else {
      alert("Inserisci un indirizzo");
    }
  });

  // Facoltativo: salva la posizione del marker (qui solo console.log)
  function saveDeliveryLocation() {
    if (deliveryMarker) {
      var pos = deliveryMarker.getLatLng();
      console.log("Posizione salvata: ", pos.lat, pos.lng);
      alert("Posizione salvata! Controlla console.");
    } else {
      alert("Nessuna posizione selezionata");
    }
  }
});
