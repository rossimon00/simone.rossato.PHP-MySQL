<?php
include('auth.php');
include('../db/connessione.php');
include('../common/navbar.php');
include('../common/header.php');

// Verifica se l'utente è loggato
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Connessione al database
$conn = getDBConnection();

// Ottieni la categoria dalla URL (se presente)
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Se la categoria non è valida, mostra un errore
$valid_categories = ['antipasti', 'primi', 'secondi', 'dessert'];
if (!in_array($category, $valid_categories)) {
    echo "Categoria non valida!";
    exit();
}

// Recupera i prodotti dalla categoria specificata
$sql = "SELECT * FROM Products WHERE category = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $category);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ucfirst($category); ?> - Menu</title>
</head>

<body style="background: linear-gradient(135deg, rgba(27, 75, 119, 0.8), rgba(0, 0, 0, 0.5));">
<main class="overflow-hidden px-0 m-0 w-100 d-flex position-relative">

<div class="sidebar-container">
    <div class="sidebar-starter">
    <i class="bi bi-layout-sidebar" style="color:white;font-size:170%"></i>
    </div>
    <div class="sidebar">
        <a href="menu.php?category=antipasti" class="menu-link">Antipasti</a>
        <a href="menu.php?category=primi" class="menu-link">Primi</a>
        <a href="menu.php?category=secondi" class="menu-link">Secondi</a>
        <a href="menu.php?category=dessert" class="menu-link">Dessert</a>
    </div>
</div>



    <div class="container py-4 change-onsidebar">
    <h2 class="text-center mb-4 text-white"><?php echo ucfirst($category); ?></h2>
    <div class="row">
    <?php if ($result->num_rows > 0): ?>
        <?php while ($product = $result->fetch_assoc()): ?>
            <div class="col mb-4">
                <div class="card h-100">
                    <!-- Overlay con dettagli principali -->
                    <div class="card-overlay p-3">
                        <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                        <p class="card-text"><strong>€ <?php echo number_format($product['price'], 2); ?></strong></p>
                        <a type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#productModal<?php echo $product['id']; ?>">
                            Dettagli
                        </a>
                    </div>
                    <!-- Immag prodotto -->
                    <div>
                        <img src="<?php echo $product['image_url']; ?>" class="card-img-top"
                            alt="<?php echo htmlspecialchars($product['name']); ?>">
                    </div>
                    <div class="card-content p-3">
                    </div>
                </div>
            </div>

            <!-- Modale associata -->
            <div class="modal fade" id="productModal<?php echo $product['id']; ?>" tabindex="-1"
                aria-labelledby="productModalLabel<?php echo $product['id']; ?>" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="productModalLabel<?php echo $product['id']; ?>">
                                <?php echo htmlspecialchars($product['name']); ?>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 text-center d-flex flex-column w-100">
                                    <p><?php echo htmlspecialchars($product['description']); ?></p>
                                    <p><strong>Prezzo:</strong> € <?php echo number_format($product['price'], 2); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="text-white">Nessun prodotto trovato in questa categoria.</p>
    <?php endif; ?>
</div>

    </div>
    </main>
    <!-- Footer -->
    <?php include('footer.php'); ?>
</body>

</html>