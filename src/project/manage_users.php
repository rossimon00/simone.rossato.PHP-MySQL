<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include('auth.php');
include('../db/connessione.php');
include('../common/navbar.php');

// Verifica se l'utente è loggato e ha il ruolo di admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Connessione al database
$conn = getDBConnection();

// Recupera tutti gli utenti tranne l'admin loggato
$sql = "SELECT * FROM Users WHERE id != ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user']['id']);
$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);

// Funzione per bannare un utente
if (isset($_GET['ban_id'])) {
    $ban_id = $_GET['ban_id'];
    $update_sql = "UPDATE Users SET banned = 1 WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("i", $ban_id);
    if ($update_stmt->execute()) {
        $_SESSION['success_message'] = "Utente bannato con successo!";
    } else {
        $_SESSION['error_message'] = "Errore durante il ban dell'utente.";
    }
    header("Location: manage_users.php");
    exit();
}

// Funzione per eliminare un utente
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_sql = "DELETE FROM Users WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $delete_id);
    if ($delete_stmt->execute()) {
        $_SESSION['success_message'] = "Utente eliminato con successo!";
    } else {
        $_SESSION['error_message'] = "Errore durante l'eliminazione dell'utente.";
    }
    header("Location: manage_users.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Utenti</title>
</head>
<body>
    <div class="container mt-5">
        <h2>Gestione Utenti</h2>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success_message']; ?>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php elseif (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['error_message']; ?>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome Utente</th>
                    <th>Ruolo</th>
                    <th>Stato</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td class="align-middle"><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                        <td>
                            <?php echo $user['banned'] ? 'Bannato' : 'Attivo'; ?>
                        </td>
                        <td>
                            <?php if ($user['banned'] == 0): ?>
                                <a href="manage_users.php?ban_id=<?php echo $user['id']; ?>" class="btn btn-warning btn-sm">Banna</a>
                            <?php else: ?>
                                <span class="text-muted">Bannato</span>
                            <?php endif; ?>
                            <a href="manage_users.php?delete_id=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm">Elimina</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
