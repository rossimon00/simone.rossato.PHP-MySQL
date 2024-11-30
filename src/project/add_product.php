<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include('auth.php');
include('../db/connessione.php');
include('../common/navbar.php');
include('../common/header.php');

// Verifica se l'utente è loggato e ha il ruolo di admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Connessione al database
$conn = getDBConnection();

// Funzione per caricare l'immagine
function uploadImage($image)
{
    $target_dir = "../assets/uploads/"; // Cartella di destinazione per l'immagine
    $target_file = $target_dir . basename($image["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Debug: mostra i dettagli dell'immagine che stai cercando di caricare
    echo '<pre>';
    var_dump($image);
    echo '</pre>';

    // Verifica se è un'immagine
    $check = getimagesize($image["tmp_name"]);
    if ($check === false) {
        return "Errore: Il file non è un'immagine.";
    }

    // Verifica la dimensione dell'immagine (5MB massimo)
    if ($image["size"] > 5000000) {
        return "Errore: L'immagine è troppo grande. La dimensione massima consentita è di 5MB.";
    }

    // Verifica se il file esiste già
    if (file_exists($target_file)) {
        return "Errore: Il file esiste già.";
    }

    // Consenti solo determinati formati di immagine
    if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        return "Errore: Sono ammessi solo file JPG, JPEG, PNG e GIF.";
    }

    // Carica l'immagine
    if (move_uploaded_file($image["tmp_name"], $target_file)) {
        return $target_file; // Restituisce il percorso dell'immagine
    } else {
        return "Errore: Si è verificato un errore durante il caricamento dell'immagine.";
    }
}

// Verifica se il form è stato inviato
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['product_name'];
    $price = $_POST['product_price'];
    $description = $_POST['product_description'];
    $category = $_POST['product_category'];
    var_dump($category);
    // Debug: mostra i dati del prodotto che vengono inviati
    echo '<pre>';
    var_dump($name, $price, $description);
    echo '</pre>';

    // Gestione dell'immagine
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $image_path = uploadImage($_FILES['product_image']);
        
        // Debug: mostra il risultato del caricamento dell'immagine
        echo '<pre>';
        var_dump($image_path);
        echo '</pre>';
        
        if (strpos($image_path, 'Errore') !== false) {
            // Mostra l'errore e fornisce più dettagli
            $_SESSION['error_message'] = "Errore nel caricamento dell'immagine: " . $image_path;
        } else {
            // Aggiungi il prodotto al database
            $sql = "INSERT INTO Products (name, price, description, category, image_url) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sdsss", $name, $price, $description, $category, $image_path);

            // Debug: mostra la query SQL prima di eseguirla
            echo '<pre>';
            var_dump($stmt);
            echo '</pre>';

            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Prodotto aggiunto con successo!";
            } else {
                // Mostra l'errore del database
                $_SESSION['error_message'] = "Errore durante l'inserimento nel database: " . $stmt->error;
            }
        }
    } else {
        // Errore nel caricamento dell'immagine
        $_SESSION['error_message'] = "Errore: Si è verificato un errore nel caricamento dell'immagine.";
    }

    // Reindirizza alla pagina di gestione prodotti
    header("Location: manage_products.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aggiungi Prodotto</title>
</head>
<body>
    <!-- Barra di navigazione -->
    <?php include('../common/navbar.php'); ?>

    <div class="container mt-5">
        <h2>Aggiungi Nuovo Prodotto</h2>

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
    </div>

</body>
</html>
