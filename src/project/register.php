<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include('auth.php');
include('../common/header.php');
include('../db/connessione.php');

// Controlla se il modulo è stato inviato
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ottieni i dati dal modulo
    $username = $_POST['username'];
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $role = $_POST['role']; // Ruolo (user/admin)

    // Controlla che la password e la conferma della password corrispondano
    if ($password !== $password_confirm) {
        $error_message = "Le password non corrispondono.";
    } else {
        // Crea la connessione al database
        $conn = getDBConnection();

        // Prepara la query per verificare se l'utente esiste già
        $stmt = $conn->prepare("SELECT * FROM Users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $existing_user = $stmt->get_result()->fetch_assoc();

        if ($existing_user) {
            // Se l'utente esiste già
            $error_message = "Questo nome utente è già in uso.";
        } else {
            // Se l'utente non esiste, crea una nuova entry
            // Cripta la password prima di salvarla
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Inserisci i dati nel database
            $stmt = $conn->prepare("INSERT INTO Users (username, password, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $hashed_password, $role);
            if ($stmt->execute()) {
                // Se la registrazione è riuscita, redirigi l'utente al login
                $_SESSION['success_message'] = "Registrazione avvenuta con successo. Puoi ora accedere.";
                header("Location: login.php");
                exit();
            } else {
                $error_message = "Errore durante la registrazione. Riprova.";
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
</head>
<body>
    <div class="container-fluid d-flex justify-content-center align-items-center" style="height: 100vh; background: url('../assets/images/register_bg.jpg') no-repeat center center;background-size:cover">
        <div class="register-container col-md-4 bg_container">
            <div class="text-center mb-4">
                <h2 class="fw-bold">Lido Azzurro</h2>
                <p>Compila il modulo per registrarti</p>
            </div>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger text-center" role="alert">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="register.php">
                <div class="form-group mb-3">
                    <label for="username" class="form-label">Nome Utente</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Inserisci il tuo nome utente" required>
                </div>
                <div class="form-group mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Inserisci la tua password" required>
                </div>
                <div class="form-group mb-3">
                    <label for="password_confirm" class="form-label">Conferma Password</label>
                    <input type="password" id="password_confirm" name="password_confirm" class="form-control" placeholder="Conferma la tua password" required>
                </div>

                <!-- Dropdown per il ruolo -->
                <div class="mb-4">
                    <label for="role" class="form-label">Ruolo</label>
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle w-100" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            Seleziona Ruolo
                        </button>
                        <ul class="dropdown-menu w-100" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item" href="#" data-role="user">Utente</a></li>
                            <li><a class="dropdown-item" href="#" data-role="admin">Amministratore</a></li>
                        </ul>
                    </div>
                    <!-- Campo nascosto per passare il valore del ruolo -->
                    <input type="hidden" id="role" name="role" value="user">
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-custom btn-lg">Registrati</button>
                </div>
            </form>

            <div class="text-center mt-4">
                <p>Hai già un account? <a href="login.php" class="link text-decoration-none">Accedi</a></p>
            </div>
        </div>
    </div>

   
    <script>
        // Gestione della dropdown per selezionare il ruolo
        document.querySelectorAll('.dropdown-item').forEach(function(item) {
            item.addEventListener('click', function() {
                var role = this.getAttribute('data-role'); // Ottieni il valore del ruolo
                document.getElementById('role').value = role; // Imposta il valore nel campo nascosto
                document.getElementById('dropdownMenuButton').textContent = this.textContent; // Cambia il testo del bottone
            });
        });
    </script>
</body>
</html>
