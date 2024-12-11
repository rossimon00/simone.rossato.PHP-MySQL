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

// Recupera tutti i prodotti
$sql = "SELECT * FROM Products";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);

// Funzione per eliminare un prodotto
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Recupera il percorso dell'immagine associata al prodotto
    $image_sql = "SELECT image_url FROM Products WHERE id = ?";
    $image_stmt = $conn->prepare($image_sql);
    $image_stmt->bind_param("i", $delete_id);
    $image_stmt->execute();
    $image_result = $image_stmt->get_result();
    $image_data = $image_result->fetch_assoc();

    if ($image_data) {
        $image_path = $image_data['image_url'];
        // Elimina il file immagine se esiste
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }

    // Elimina il prodotto dal database
    $delete_sql = "DELETE FROM Products WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $delete_id);
    if ($delete_stmt->execute()) {
        $_SESSION['success_message'] = "Prodotto eliminato con successo!";
    } else {
        $_SESSION['error_message'] = "Errore durante l'eliminazione del prodotto.";
    }

    header("Location: manage_products.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Prodotti</title>
</head>
<body style="background:url('../assets/images/black_background_cafe.jpg') no-repeat center center; background-size: cover;">
<div class="container-fluid row" style="padding-top: 2vh;">
    <div class="container">    
        <h2>Gestione Prodotti</h2>

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

        <!-- Tabella dei prodotti -->
        <table class="table table-striped table-dark">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Prezzo</th>
                    <th>Descrizione</th>
                    <th>Categoria</th> <!-- Categoria aggiunta -->
                    <th>Immagine</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['id']); ?></td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td>€ <?php echo number_format($product['price'], 2); ?></td>
                        <td><?php echo htmlspecialchars($product['description']); ?></td>
                        <td><?php echo htmlspecialchars($product['category']); ?></td> <!-- Categoria visualizzata -->
                        <td>
                            <img src="<?php echo $product['image_url']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" width="100">
                        </td>
                        <td>
                            <a href="manage_products.php?delete_id=<?php echo $product['id']; ?>" class="btn btn-danger btn-sm">Elimina</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Button trigger modal -->
        <button type="button" class="btn btn-add" data-bs-toggle="modal" style="position:absolute;top:12vh;right:2vw" data-bs-target="#exampleModalCenter">
            Aggiungi Prodotto
        </button>

    </div>
</div>
</body>
</html>

<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Aggiungi Prodotto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="add_product.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="product_name" class="form-label">Nome Prodotto</label>
                        <input type="text" class="form-control" id="product_name" name="product_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="product_price" class="form-label">Prezzo</label>
                        <input type="number" class="form-control" id="product_price" name="product_price" required>
                    </div>
                    <div class="mb-3">
                        <label for="product_description" class="form-label">Descrizione</label>
                        <textarea class="form-control" id="product_description" name="product_description" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="product_category" class="form-label">Categoria</label>
                        <select class="form-select" id="product_category" name="product_category" required>
                            <option value="primi">Primi</option>
                            <option value="antipasti">Antipasti</option>
                            <option value="secondi">Secondi</option>
                            <option value="dessert">Dessert</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="product_image" class="form-label">Immagine</label>
                        <input type="file" class="form-control" id="product_image" name="product_image" required>
                    </div>
                    <div class="d-flex justify-content-center w-100">
                    <button type="submit" class="btn btn-add">Aggiungi Prodotto</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
            </div>
        </div>
    </div>
</div>
