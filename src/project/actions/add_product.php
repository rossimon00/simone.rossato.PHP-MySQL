<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('../auth.php');
include('../../db/connessione.php');

// Verifica se l'utente è loggato e ha il ruolo di admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: /login.php");
    exit();
}

// Connessione al database
$conn = getDBConnection();

// Funzione per caricare l'immagine
function uploadImage($image)
{
    $target_dir = "../../assets/uploads/";
    $db_target_dir = "../assets/uploads/";
    $file_name = basename($image["name"]);
    $target_file = $target_dir . $file_name;
    $db_target_file = $db_target_dir . $file_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Mostra info in console
    echo "<script>console.log(" . json_encode($image) . ");</script>";

    // Verifica se è un'immagine
    $check = getimagesize($image["tmp_name"]);
    if ($check === false) {
        return "Errore: Il file non è un'immagine.";
    }

    // Verifica dimensione massima
    if ($image["size"] > 5000000) {
        return "Errore: L'immagine è troppo grande. Max 5MB.";
    }

    // Verifica se esiste già
    if (file_exists($target_file)) {
        return "Errore: Il file esiste già.";
    }

    // Estensioni consentite
    if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        return "Errore: Solo JPG, JPEG, PNG, GIF.";
    }

    // Carica immagine
    if (move_uploaded_file($image["tmp_name"], $target_file)) {
        return $db_target_file; // Percorso salvato
    } else {
        return "Errore: Upload fallito.";
    }
}

// Se è stato inviato il form
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['product_name'] ?? '';
    $price = $_POST['product_price'] ?? '';
    $description = $_POST['product_description'] ?? '';
    $category = $_POST['product_category'] ?? '';

    // Debug client
    echo "<script>console.log('Ricevuto: ', " . json_encode($_POST) . ");</script>";

    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
        $image_path = uploadImage($_FILES['product_image']);
        echo "<script>console.log('Upload result: " . $image_path . "');</script>";

        if (str_starts_with($image_path, 'Errore')) {
            $_SESSION['error_message'] = $image_path;
        } else {
            $sql = "INSERT INTO Products (name, price, description, category, image_url) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sdsss", $name, $price, $description, $category, $image_path);

            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Prodotto aggiunto con successo!";
            } else {
                $_SESSION['error_message'] = "Errore DB: " . $stmt->error;
            }
        }
    } else {
        $_SESSION['error_message'] = "Errore upload immagine (nessun file o errore).";
        echo "<script>console.log('Errore $_FILES: ', " . json_encode($_FILES) . ");</script>";
    }

    header("Location: ../manage_products.php");
    exit();
}
?>