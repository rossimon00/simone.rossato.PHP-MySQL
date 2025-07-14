<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include('auth.php');
include('../common/header.php');
include('../db/connessione.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $role = $_POST['role'];
    $nome = trim($_POST['nome']);
    $cognome = trim($_POST['cognome']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);

    if ($password !== $password_confirm) {
        $error_message = "Le password non corrispondono.";
    } elseif (!$nome || !$cognome || !$email || !$username) {
        $error_message = "Tutti i campi obbligatori devono essere compilati.";
    } else {
        $conn = getDBConnection();

        $stmt = $conn->prepare("SELECT * FROM Users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $existing_user = $stmt->get_result()->fetch_assoc();

        if ($existing_user) {
            $error_message = "Username o email già in uso.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO Users (username, password, role, nome, cognome, email, telefono) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $username, $hashed_password, $role, $nome, $cognome, $email, $telefono);

            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Registrazione avvenuta con successo.";
                header("Location: login.php");
                exit();
            } else {
                $error_message = "Errore durante la registrazione.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="w-full d-flex flex-column" style="min-height: 100vh;">
    <div class="flex-grow-1 d-flex justify-content-center align-items-center"
        style="background: url('../assets/images/register_bg.jpg') no-repeat center center;background-size:cover">
        <div class="register-container col-md-5 bg-light p-5 rounded-4 shadow">
            <div class="text-center mb-4">
                <h2 class="fw-bold">Lido Azzurro</h2>
                <p>Compila il modulo per registrarti</p>
            </div>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger text-center"><?= $error_message; ?></div>
            <?php endif; ?>

            <form method="POST" action="register.php" id="registrationForm">
                <div class="row g-3">
                    <!-- Colonna sinistra -->
                    <div class="col-md-6">
                        <div class="form-group mb-2">
                            <label for="username" class="form-label">Nome Utente *</label>
                            <input type="text" id="username" name="username" class="form-control" required>
                        </div>

                        <div class="form-group mb-2">
                            <label for="nome" class="form-label">Nome *</label>
                            <input type="text" id="nome" name="nome" class="form-control" required>
                        </div>

                        <div class="form-group mb-2">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>

                        <div class="form-group mb-2">
                            <label for="password" class="form-label">Password *</label>
                            <input type="password" id="password" name="password" class="form-control" required>
                        </div>
                    </div>

                    <!-- Colonna destra -->
                    <div class="col-md-6">
                        <div class="form-group mb-2">
                            <label for="cognome" class="form-label">Cognome *</label>
                            <input type="text" id="cognome" name="cognome" class="form-control" required>
                        </div>

                        <div class="form-group mb-2">
                            <label for="telefono" class="form-label">Telefono</label>
                            <input type="tel" id="telefono" name="telefono" class="form-control" pattern="[0-9]{10}" >
                        </div>

                        <div class="form-group mb-2">
                            <label for="password_confirm" class="form-label">Conferma Password *</label>
                            <input type="password" id="password_confirm" name="password_confirm" class="form-control"
                                required>
                        </div>

                        <!-- Dropdown ruolo -->
                        <div class="mb-2">
                            <label for="role" class="form-label">Ruolo *</label>
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle w-100" type="button"
                                    id="dropdownRoleButton" data-bs-toggle="dropdown" aria-expanded="false">
                                    Seleziona Ruolo
                                </button>
                                <ul class="dropdown-menu w-100" aria-labelledby="dropdownRoleButton">
                                    <li><a class="dropdown-item" href="#" data-role="user">Utente</a></li>
                                    <li><a class="dropdown-item" href="#" data-role="manager">Gestore</a></li>
                                </ul>
                            </div>
                            <input type="hidden" id="role" name="role" value="user">
                        </div>
                    </div>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-success btn-lg">Registrati</button>
                </div>
            </form>


            <div class="text-center mt-4">
                <p>Hai già un account? <a href="login.php" class="link text-decoration-none">Accedi</a></p>
            </div>
        </div>
    </div>

    <script>
        const form = document.getElementById('registrationForm');

        form.addEventListener('submit', function (event) {
            const role = document.getElementById('role').value;

            if (role === 'manager') {
                event.preventDefault();
                Swal.fire({
                    title: "Richiesta inviata",
                    text: "L'account gestore sarà attivo solo dopo l'approvazione dell'amministratore.",
                    icon: "info",
                    confirmButtonText: "OK"
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            }
        });

        document.querySelectorAll('.dropdown-item').forEach(function (item) {
            item.addEventListener('click', function () {
                const role = this.getAttribute('data-role');
                document.getElementById('role').value = role;
                document.getElementById('dropdownMenuButton').textContent = this.textContent;
            });
        });
    </script>
</body>

</html>