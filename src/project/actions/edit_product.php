<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('../auth.php');
include('../../db/connessione.php');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$conn = getDBConnection();

// Funzione uploadImage la prendi dal tuo codice precedente
function uploadImage($image) {
    $target_dir = "../assets/uploads/";
    $target_file = $target_dir . basename($image["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Verifica se è immagine
    $check = getimagesize($image["tmp_name"]);
    if ($check === false) {
        return "Errore: Il file non è un'immagine.";
    }
    if ($image["size"] > 5000000) {
        return "Errore: Immagine troppo grande.";
    }
    if (file_exists($target_file)) {
        return "Errore: File già esistente.";
    }
    if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        return "Errore: Formato non consentito.";
    }
    if (move_uploaded_file($image["tmp_name"], $target_file)) {
        return $target_file;
    }
    return "Errore nel caricamento dell'immagine.";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = intval($_POST['product_id']);
    $name = $_POST['product_name'];
    $price = $_POST['product_price'];
    $description = $_POST['product_description'];
    $category = $_POST['product_category'];

    // Prima prendi il percorso immagine esistente
    $stmtImg = $conn->prepare("SELECT image_url FROM Products WHERE id = ?");
    $stmtImg->bind_param("i", $id);
    $stmtImg->execute();
    $resImg = $stmtImg->get_result();
    $oldImage = null;
    if ($row = $resImg->fetch_assoc()) {
        $oldImage = $row['image_url'];
    }
    $stmtImg->close();

    // Se arriva una nuova immagine, caricala
    $newImagePath = $oldImage;
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
        $uploadResult = uploadImage($_FILES['product_image']);
        if (strpos($uploadResult, 'Errore') === false) {
            $newImagePath = $uploadResult;

            // Elimina vecchia immagine se esiste
            if ($oldImage && file_exists($oldImage)) {
                unlink($oldImage);
            }
        } else {
            $_SESSION['error_message'] = $uploadResult;
            header("Location: manage_products.php");
            exit();
        }
    }

    // Aggiorna prodotto
    $sql = "UPDATE Products SET name = ?, price = ?, description = ?, category = ?, image_url = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdsssi", $name, $price, $description, $category, $newImagePath, $id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Prodotto aggiornato con successo!";
    } else {
        $_SESSION['error_message'] = "Errore aggiornamento prodotto: " . $stmt->error;
    }

    $stmt->close();
    header("Location: ../manage_products.php");
    exit();
}
?>
