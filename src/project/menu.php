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
<body style="padding-top: 10vh; background: linear-gradient(135deg, rgba(27, 75, 119, 0.8), rgba(0, 0, 0, 0.5));">
<main>
    <div class="container mt-5">
        <h2 class="text-center mb-4 text-white"><?php echo ucfirst($category); ?></h2>

        <section id="slider">
            <?php if ($result->num_rows > 0): ?>
                <?php $i = 1; // Contatore per gli ID univoci ?>
                <?php while ($product = $result->fetch_assoc()): ?>
                    <input type="radio" name="slider" id="s<?php echo $i; ?>" <?php echo $i === 1 ? 'checked' : ''; ?>>
                    <label for="s<?php echo $i; ?>" class="card-label">
                        <div class="card">
                            <img src="<?php echo $product['image_url']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <div class="card-body d-flex align-items-center flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                                <p class="card-text"><strong>€ <?php echo number_format($product['price'], 2); ?></strong></p>
                            </div>
                            </div>
                    </label>
                    <?php $i++; ?>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Nessun prodotto trovato in questa categoria.</p>
            <?php endif; ?>
        </section>
    </div>
</main>
<?php include('footer.php'); ?>
</body>
</html>
