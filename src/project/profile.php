<?php
// Attiviamo la visualizzazione degli errori per facilitare il debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('auth.php');
include('../db/connessione.php');
include('../common/navbar.php');

$conn = getDBConnection();

$userId = $_SESSION['user']['id'];

$stmt = $conn->prepare("SELECT username, email, role, nome, cognome,telefono FROM Users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();
$conn->close();

if (!$user) {
    echo "Utente non trovato.";
    exit;
}
?>

<body
    style="height:100vh;display:flex;flex-direction:column;background: url('../assets/images/register_bg.jpg') no-repeat center center;background-size:cover">
    <div style="display:flex;flex:1; justify-content: center;align-items:center;flex-direction:column;">
        <div class="profile-card">
            <h2><i class="bi bi-person-circle"></i> Il Mio Profilo</h2>
            <dl class="row profile-list">
                <dt class="col-4"><i class="bi bi-person-fill icon"></i>Username</dt>
                <dd class="col-8"><?= htmlspecialchars($user['username']) ?></dd>

                <dt class="col-4"><i class="bi bi-envelope-fill icon"></i>Email</dt>
                <dd class="col-8"><?= htmlspecialchars($user['email']) ?></dd>

                <dt class="col-4"><i class="bi bi-person-badge-fill icon"></i>Nome</dt>
                <dd class="col-8"><?= htmlspecialchars($user['nome']) ?></dd>

                <dt class="col-4"><i class="bi bi-person-bounding-box icon"></i>Cognome</dt>
                <dd class="col-8"><?= htmlspecialchars($user['cognome']) ?></dd>

                <dt class="col-4"><i class="bi bi-telephone-fill icon"></i>Telefono</dt>
                <dd class="col-8"><?= htmlspecialchars($user['telefono']) ?></dd>

                <dt class="col-4"><i class="bi bi-shield-lock-fill icon"></i>Ruolo</dt>
                <dd class="col-8 text-capitalize"><?= htmlspecialchars($user['role']) ?></dd>
            </dl>

            <a href="edit_profile.php" class="btn btn-primary btn-edit mt-3">
                <i class="bi bi-pencil-square me-2"></i>Modifica Profilo
            </a>
        </div>
    </div>


</body>

</html>