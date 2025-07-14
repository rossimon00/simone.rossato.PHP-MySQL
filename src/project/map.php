<?php
include('auth.php');
include('../db/connessione.php');
include('../common/navbar.php');

// Verifica se l'utente è loggato
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
?>
<body>

<div id="searchBox" class="position-absolute start-50 translate-middle-x mt-3 w-50 z-1" style="top:200px">
  <div class="input-group shadow">
    <input type="text" id="addressInput" class="form-control" placeholder="Inserisci indirizzo per la consegna" autocomplete="off" />
    <button id="searchBtn" class="btn btn-primary">Cerca</button>
  </div>
  <div id="suggestions" class="list-group position-relative"></div>
</div>

<div id="map" class="h-100 z-0" style="max-height:calc(100% - 305px);"></div>

<?php
include('footer.php')
?>

</body>
