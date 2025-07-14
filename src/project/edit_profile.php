<?php
include('auth.php');
include('../db/connessione.php');
include('../common/navbar.php');

$conn = getDBConnection();

$userId = $_SESSION['user']['id'] ?? null;
if (!$userId) {
    header('Location: login.php');
    exit;
}

$success = '';
$error = '';

$stmt = $conn->prepare("SELECT username, email, role, nome, cognome, telefono FROM Users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    echo "Utente non trovato.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $cognome = trim($_POST['cognome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (!$nome || !$cognome || !$email) {
        $error = "Nome, Cognome e Email sono obbligatori.";
    } elseif ($password !== $password_confirm) {
        $error = "Le password non coincidono.";
    } else {
        if ($password) {
            $hashedPwd = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE Users SET nome=?, cognome=?, email=?, telefono=?, password=? WHERE id=?");
            $stmt->bind_param("sssssi", $nome, $cognome, $email, $telefono, $hashedPwd, $userId);
        } else {
            $stmt = $conn->prepare("UPDATE Users SET nome=?, cognome=?, email=?, telefono=? WHERE id=?");
            $stmt->bind_param("ssssi", $nome, $cognome, $email, $telefono, $userId);
        }

        if ($stmt->execute()) {
            $success = "Profilo aggiornato con successo.";
            $user['nome'] = $nome;
            $user['cognome'] = $cognome;
            $user['email'] = $email;
            $user['telefono'] = $telefono;
        } else {
            $error = "Errore nell'aggiornamento del profilo.";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Modifica Profilo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
</head>

<body
    style="background: url('../assets/images/register_bg.jpg') no-repeat center center; background-size: cover; min-height:100vh; display:flex;width: 100%;flex-direction:column">
    <div class="flex-grow-1"
        style=" display:flex; flex-direction:column; justify-content:center; align-items:center; width:100%;">
        <?php if ($success): ?>
   <script>
        document.addEventListener("DOMContentLoaded", function () {
            Swal.fire({
                icon: 'success',
                title: 'Successo!',
                text: <?= json_encode($success) ?>,
                confirmButtonColor: '#198754'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'profile.php';
                }
            });
        });
    </script>        <?php endif; ?>
        <?php if ($error): ?>
   <script>
        document.addEventListener("DOMContentLoaded", function () {
            Swal.fire({
                icon: 'error',
                title: 'Errore!',
                text: <?= json_encode($error) ?>,
                confirmButtonColor: 'red'
            });
        });
    </script>        <?php endif; ?>
        <div class="card shadow-lg rounded-4 p-4 bg-white" style="max-width: 900px; width: 100%; min-height:75%;">
            <h3 class="mb-4 text-center" style="color:#11294d;">
                <i class="bi bi-pencil-square me-2"></i> Modifica Profilo
            </h3>

            <form method="POST" novalidate>
                <div class="mb-3">
                    <label for="username" class="form-label fs-4">Username</label>
                    <input type="text" readonly class="form-control fs-5" id="username" name="username"
                        value="<?= htmlspecialchars($user['username']) ?>">
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <label for="nome" class="form-label fs-4">Nome *</label>
                        <input type="text" class="form-control form-control-lg" id="nome" name="nome" required
                            value="<?= htmlspecialchars($_POST['nome'] ?? $user['nome']) ?>">
                    </div>

                    <div class="col-md-6">
                        <label for="cognome" class="form-label fs-4">Cognome *</label>
                        <input type="text" class="form-control form-control-lg" id="cognome" name="cognome" required
                            value="<?= htmlspecialchars($_POST['cognome'] ?? $user['cognome']) ?>">
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label fs-4">Email *</label>
                        <input type="email" class="form-control form-control-lg" id="email" name="email" required
                            value="<?= htmlspecialchars($_POST['email'] ?? $user['email']) ?>">
                    </div>

                    <div class="col-md-6">
                        <label for="telefono" class="form-label fs-4">Telefono</label>
                        <input type="tel" class="form-control form-control-lg" id="telefono" name="telefono"
                            value="<?= htmlspecialchars($_POST['telefono'] ?? $user['telefono']) ?>">
                    </div>

                    <div class="col-md-6">
                        <label for="password" class="form-label fs-4">Nuova Password</label>
                        <input type="password" class="form-control form-control-lg" id="password" name="password"
                            placeholder="Lascia vuoto per mantenere la password attuale">
                    </div>

                    <div class="col-md-6">
                        <label for="password_confirm" class="form-label fs-4">Conferma Nuova Password</label>
                        <input type="password" class="form-control form-control-lg" id="password_confirm"
                            name="password_confirm" placeholder="Ripeti la nuova password">
                    </div>
                </div>

                <div class="d-flex justify-content-center mt-4 mb-4">
                    <button type="submit" class="btn btn-success px-5 rounded-4 fs-5">Salva modifiche</button>
                </div>
            </form>
        </div>
    </div>


</body>

</html>