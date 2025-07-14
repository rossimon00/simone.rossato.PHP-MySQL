<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include('../db/connessione.php');
include('auth.php');
include('utils.php');

$conn = getDBConnection();
if (!$conn) {
  die("Errore di connessione: " . mysqli_connect_error());
  exit();
}

$max_ombrelloni = 20;
$max_lettini = 50;
$prezzo_ombrellone = 12;
$prezzo_lettino = 10;

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $data_input = $_POST['data'] ?? '';
  $data = convertDateToMySQLFormat($data_input);

  $num_ombrelloni = intval($_POST['num_ombrelloni'] ?? 0);
  $num_lettini = intval($_POST['num_lettini'] ?? 0);
  $nome = trim($_POST['nome'] ?? '');
  $cognome = trim($_POST['cognome'] ?? '');
  $telefono = trim($_POST['telefono'] ?? '');

  if (!$data) {
    $errors[] = "Devi selezionare una data.";
  }
  if ($num_ombrelloni < 0 || $num_lettini < 0) {
    $errors[] = "Il numero di ombrelloni o lettini non può essere negativo.";
  }
  if (!$nome || !$cognome) {
    $errors[] = "Nome e cognome sono obbligatori.";
  }

  if (empty($errors)) {
    $stmt = $conn->prepare("SELECT COALESCE(SUM(num_ombrelloni),0), COALESCE(SUM(num_lettini),0) FROM PrenotazioniMare WHERE data = ?");
    if (!$stmt) {
      $errors[] = "Errore nella preparazione della query di controllo: " . $conn->error;
    } else {
      $stmt->bind_param("s", $data);
      $stmt->execute();
      $stmt->bind_result($tot_ombrelloni, $tot_lettini);
      $stmt->fetch();
      $stmt->close();

      $ombrelloni_disponibili = $max_ombrelloni - $tot_ombrelloni;
      $lettini_disponibili = $max_lettini - $tot_lettini;

      if ($num_ombrelloni > $ombrelloni_disponibili) {
        $errors[] = "Spiacente, per questa data sono disponibili solo $ombrelloni_disponibili ombrelloni.";
      }
      if ($num_lettini > $lettini_disponibili) {
        $errors[] = "Spiacente, per questa data sono disponibili solo $lettini_disponibili lettini.";
      }

      if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO PrenotazioniMare (data, num_ombrelloni, num_lettini, nome, cognome,telefono) VALUES (?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
          $errors[] = "Errore nella preparazione della query di inserimento: " . $conn->error;
        } else {
          $stmt->bind_param("siisss", $data, $num_ombrelloni, $num_lettini, $nome, $cognome, $telefono);
          if ($stmt->execute()) {
            $totale = $num_ombrelloni * $prezzo_ombrellone + $num_lettini * $prezzo_lettino;
            $success = "Prenotazione confermata! Totale da pagare: €$totale.";
          } else {
            $errors[] = "Errore durante la prenotazione: " . $stmt->error;
          }
          $stmt->close();
        }
      }
    }
  }
}
include('../common/navbar.php');
?>

<!DOCTYPE html>
<html lang="it">

<head>
  <meta charset="UTF-8" />
  <title>Prenotazione Ombrelloni e Lettini</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="margin: 0; display: flex; flex-direction: column; min-height: 100vh; position: relative;">
  <div style="height:90%; padding: 20px;position:relative;background-image: url('../assets/images/beach_background.png');
     background-size: cover;
     background-position: center bottom;
     background-repeat: no-repeat;">
    <h1 class="header-title">Prenotazione Ombrelloni e Lettini</h1>

    <form method="POST" id="prenotazione" class="mx-auto bg-white p-4 rounded shadow" style="max-width: 500px;">
      <div class="mb-3">
        <label for="data" class="form-label">Data</label>
        <input type="text" id="datepicker" placeholder="Seleziona una data" class="form-control" name="data" required
          value="<?= htmlspecialchars($_POST['data'] ?? '') ?>">
      </div>

      <div class="mb-3">
        <label for="num_ombrelloni" class="form-label">Numero Ombrelloni</label>
        <input type="number" class="form-control" id="num_ombrelloni" name="num_ombrelloni" min="0"
          max="<?= $max_ombrelloni ?>" value="<?= htmlspecialchars($_POST['num_ombrelloni'] ?? 0) ?>">
      </div>

      <div class="mb-3">
        <label for="num_lettini" class="form-label">Numero Lettini</label>
        <input type="number" class="form-control" id="num_lettini" name="num_lettini" min="0" max="<?= $max_lettini ?>"
          value="<?= htmlspecialchars($_POST['num_lettini'] ?? 0) ?>">
      </div>

      <div class="mb-3">
        <label for="nome" class="form-label">Nome</label>
        <input type="text" class="form-control" id="nome" name="nome" required
          value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>">
      </div>

      <div class="mb-3">
        <label for="cognome" class="form-label">Cognome</label>
        <input type="text" class="form-control" id="cognome" name="cognome" required
          value="<?= htmlspecialchars($_POST['cognome'] ?? '') ?>">
      </div>

      <!-- Nuovo campo numero di telefono -->
      <div class="mb-3">
        <label for="telefono" class="form-label">Numero di Telefono</label>
        <input type="tel" class="form-control" id="telefono" name="telefono" required
          placeholder="Inserisci il numero di telefono" value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>"
          pattern="[0-9+\-\s]{6,20}">
      </div>

      <button type="submit" class="btn btn-primary w-100">Prenota</button>
    </form>

  </div>

  <?php include('footer.php'); ?>

  <script>
    document.getElementById('prenotazione').addEventListener('submit', function (event) {
      const ombrelloni = parseInt(document.getElementById('num_ombrelloni').value) || 0;
      const lettini = parseInt(document.getElementById('num_lettini').value) || 0;

      if (lettini < ombrelloni || lettini == 0) {
        event.preventDefault();  // blocca invio form
        Swal.fire({
          icon: 'error',
          title: 'Attenzione: Prenotazione non effettuata',
          html: `<div style="color:#a71313">Ogni ombrellone ha almeno un lettino compreso obbligatorio!</div>`,
          confirmButtonColor: '#d33'
        });
      }
    });


    // Imposta gradiente al footer
    const footer = document.querySelector('footer');
    if (footer) {
      footer.style.setProperty(
        'background',
        'linear-gradient(to bottom, rgb(0, 197, 179), #00b5c5)',
        'important'
      );
    }

    // Mostra errori da PHP con SweetAlert2
    <?php if (!empty($errors)): ?>
      event.preventDefault();
      const phpErrors = <?= json_encode($errors) ?>;
      const errorList = phpErrors.map(msg => `<li>${msg}</li>`).join('');
      Swal.fire({
        icon: 'error',
        title: 'Attenzione: Prenotazione non effettuata',
        html: `<ul style="list-style-type: none; text-align: left; padding-left: 0;color:#a71313;font-size:medium">${errorList}</ul>`,
        confirmButtonColor: '#d33'
      });
    <?php endif; ?>

    // Mostra messaggio di successo con SweetAlert2
    <?php if (!empty($success)): ?>
      Swal.fire({
        icon: 'success',
        title: 'Prenotazione confermata!',
        text: <?= json_encode($success) ?>,
        confirmButtonColor: '#3085d6'
      });
    <?php endif; ?>
  </script>

</body>

</html>