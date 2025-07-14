<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('auth.php');
include('../db/connessione.php');
include('../common/navbar.php');

$conn = getDBConnection();

// Data selezionata (GET), default = oggi
$dataScelta = $_GET['data'] ?? date('Y-m-d');

// Prendi le prenotazioni Ristorante per la data scelta
$stmtRistorante = $conn->prepare("SELECT nome, cognome, data, ora, num_persone FROM PrenotazioniRistorante WHERE data = ? ORDER BY ora ASC");
$stmtRistorante->bind_param("s", $dataScelta);
$stmtRistorante->execute();
$resultRistorante = $stmtRistorante->get_result();
$ristoranteBookings = $resultRistorante->fetch_all(MYSQLI_ASSOC);
$stmtRistorante->close();

// Prendi le prenotazioni Mare per la data scelta
$stmtMare = $conn->prepare("SELECT nome, cognome, data, num_ombrelloni, num_lettini FROM PrenotazioniMare WHERE data = ? ORDER BY data DESC");
$stmtMare->bind_param("s", $dataScelta);
$stmtMare->execute();
$resultMare = $stmtMare->get_result();
$mareBookings = $resultMare->fetch_all(MYSQLI_ASSOC);
$stmtMare->close();

$conn->close();
?>

<body>
    <div class="container py-5">
        <h2 class="mb-4">Prenotazioni del <?= date('d/m/Y', strtotime($dataScelta)) ?></h2>

        <!-- Form per selezionare la data -->
        <form class="mb-4" method="get" id="dateForm">
            <div class="row g-2 align-items-end">
                <div class="col-auto">
                    <label for="data" class="form-label">Seleziona data:</label>
                    <input type="date" id="data" name="data" value="<?= htmlspecialchars($dataScelta) ?>"
                        class="form-control" required>
                </div>
            </div>
        </form>
        <!-- Nav Tabs -->
        <ul class="nav nav-tabs" id="bookingTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="ristorante-tab" data-bs-toggle="tab" data-bs-target="#ristorante"
                    type="button" role="tab">Ristorante</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="mare-tab" data-bs-toggle="tab" data-bs-target="#mare" type="button"
                    role="tab">Stabilimento balneare</button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content mt-4" id="bookingTabsContent">
            <!-- Ristorante -->
            <div class="tab-pane fade show active" id="ristorante" role="tabpanel" aria-labelledby="ristorante-tab">
                <?php if (count($ristoranteBookings) > 0): ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nome Prenotazione</th>
                                <th>Data</th>
                                <th>Orario</th>
                                <th>Persone</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ristoranteBookings as $booking): ?>
                                <tr>
                                    <td><?= htmlspecialchars($booking['nome']) . ' ' . htmlspecialchars($booking['cognome']) ?>
                                    </td>
                                    <td><?= (new DateTime($booking['data']))->format('d/m/Y') ?></td>
                                    <td><?= (new DateTime($booking['ora']))->format('H:i') ?></td>
                                    <td><?= htmlspecialchars($booking['num_persone']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>Nessuna prenotazione.</p>
                <?php endif; ?>
            </div>

            <!-- Mare -->
            <div class="tab-pane fade" id="mare" role="tabpanel" aria-labelledby="mare-tab">
                <?php if (count($mareBookings) > 0): ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nome Prenotazione</th>
                                <th>Data</th>
                                <th>Ombrelloni</th>
                                <th>Lettini</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($mareBookings as $booking): ?>
                                <tr>
                                    <td><?= htmlspecialchars($booking['nome']) . ' ' . htmlspecialchars($booking['cognome']) ?>
                                    </td>
                                    <td><?= (new DateTime($booking['data']))->format('d/m/Y') ?></td>
                                    <td><?= htmlspecialchars($booking['num_ombrelloni']) ?></td>
                                    <td><?= htmlspecialchars($booking['num_lettini']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>Nessuna prenotazione.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

<script>
    // Invia il form al cambio della data
    document.getElementById('data').addEventListener('change', function () {
        document.getElementById('dateForm').submit();
    });
</script>
</body>

</html>