<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('auth.php');
// Verifica se l'utente è loggato
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
include('../db/connessione.php');

include('../common/navbar.php');
?>


<body style="background-image: url('../assets/images/sea_cart.png');
     background-size: cover;
     background-position: center bottom;
     background-repeat: no-repeat;
     " class="position-absolute w-100 h-100">

<div class="d-flex align-items-center flex-column" style="min-height:calc(100% - 305px);">
  <div class="container py-5" style="padding:0px !important; margin: 0px !important;margin-top: 4vh !important;">
    <div id="orderContainer" class="order-container">
        <div id="deliveryInfo" class="text-center mb-3 d-flex align-items-center w-100 flex-column">
    <h1 class="mb-4 text-white">Il tuo ordine</h1>
  <h5 class="text-white">Indirizzo di consegna:</h5>
  <p id="deliveryAddress" class="text-white fw-bold"></p>

  <button id="selectAddressBtn" class="btn btn-outline-light mt-2">Seleziona punto di consegna</button>
  <button id="changeAddressBtn" class="btn btn-outline-warning mt-2 d-none">Cambia indirizzo</button>
</div>

      <p id="emptyMsg" class="text-white">Il carrello è vuoto.</p>
      <ul id="orderList" class="order-list d-none"></ul>
      <div id="totalAmount" class="fs-4 fw-bold mt-3 w-100 text-center text-white"></div>

        <div class="text-center mb-3 d-flex align-items-center w-100 flex-column">
     <button id="paymentBtn" class="btn btn-light text-black mt-4 mb-4">Procedi al pagamento</button>
  <button id="clearCartBtn" class="btn btn-secondary">Svuota carrello</button>
  </div>
    </div>
  </div>
 
</div>



</body>
