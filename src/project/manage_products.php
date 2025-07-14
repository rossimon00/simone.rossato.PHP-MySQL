<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('../db/connessione.php');
include('auth.php');


if (isset($_GET['fetch_product'])) {
    $id = intval($_GET['fetch_product']);
    $conn = getDBConnection();

    $stmt = $conn->prepare("SELECT * FROM Products WHERE id = ?");
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['error' => 'Errore nella preparazione della query']);
        exit;
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if ($product) {
        header('Content-Type: application/json');
        echo json_encode($product);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Prodotto non trovato']);
    }
    exit();

}


// Verifica se l'utente è loggato e ha il ruolo di admin
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'admin' && $_SESSION['user']['role'] !== 'manager')) {
    var_dump(value: !isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'admin' || $_SESSION['user']['role'] !== 'manager'));
    var_dump($_SESSION['user']);
    //header("Location: ../login.php");
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

include('../common/navbar.php');
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Prodotti</title>
</head>

<body
    style="background:url('../assets/images/black_background_cafe.jpg') no-repeat center center; background-size: cover;">
    <div class="container-fluid row" style="padding-top: 2vh;">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center my-2">
                <h2>Gestione Prodotti</h2> <button type="button" class="btn btn-add" id="btnAddProduct">
                    Aggiungi Prodotto
                </button>
            </div>

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
                                <img src="<?php echo $product['image_url']; ?>"
                                    alt="<?php echo htmlspecialchars($product['name']); ?>" width="100">
                            </td>
                            <td>
                                <button class="btn btn-warning btn-sm btn-edit-product"
                                    data-id="<?php echo htmlspecialchars($product['id']); ?>">
                                    Modifica
                                </button>

                                <a href="manage_products.php?delete_id=<?php echo $product['id']; ?>"
                                    class="btn btn-danger btn-sm">Elimina</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>
    </div>
</body>

</html>
<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-header text-white rounded-top-4" style="background:#11294d">
                <h3 class="modal-title" id="exampleModalLongTitle">Aggiungi Prodotto</h3>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form action="actions/add_product.php" id="productForm" method="POST" enctype="multipart/form-data">
                    <div class="row g-4">
                        <!-- Colonna Sinistra -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="product_name" class="form-label">Nome Prodotto</label>
                                <input type="text" class="form-control" id="product_name" name="product_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="product_price" class="form-label">Prezzo</label>
                                <input type="number" class="form-control" id="product_price" step="0.1" name="product_price"
                                    required>
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
                        </div>

                        <!-- Colonna Destra -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="product_description" class="form-label">Descrizione</label>
                                <textarea class="form-control" id="product_description" name="product_description"
                                    rows="6" required></textarea>
                            </div>
                            <div class="mb-3" style="display:flex;flex-direction:column;align-items:center">
                                <label for="product_image" class="form-label">Immagine</label>
                                <input type="file" class="form-control" id="product_image" name="product_image"
                                    required>
                                <img id="previewImage" src="" alt="Immagine attuale"
                                    class="img-fluid mt-2 rounded border" style="display:none; max-height: 200px;">
                                <h7 id="imageLabel">Immagine Attuale</h7>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="product_id" id="product_id">

                    <div class="d-flex justify-content-center mt-4">
                        <button type="submit" class="btn btn-success px-4" id="submitBtn">Aggiungi Prodotto</button>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Chiudi</button>
            </div>
        </div>
    </div>
</div>



<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editButtons = document.querySelectorAll('.btn-edit-product');
        const addButton = document.getElementById('btnAddProduct');

        const productForm = document.getElementById('productForm');
        const productImageInput = document.getElementById('product_image');
        const modalTitle = document.querySelector('#exampleModalCenter .modal-title');
        const submitBtn = productForm.querySelector('button[type="submit"]');
        const previewImage = document.getElementById('previewImage');
        const imageLabel = document.getElementById('imageLabel');
        const modalElement = document.getElementById('exampleModalCenter');
        const bootstrapModal = new bootstrap.Modal(modalElement);

        // Modal mode: 'add' or 'edit'
        let formMode = 'add';

        // Funzione per aprire modale in modalità aggiunta
        function openAddModal() {
            formMode = 'add';

            productForm.reset();
            productForm.action = 'actions/add_product.php';
            productImageInput.required = true;
            document.getElementById('product_id').value = '';

            modalTitle.textContent = 'Aggiungi Prodotto';
            submitBtn.textContent = 'Aggiungi Prodotto';

            // Nascondi immagine preview
            previewImage.style.display = 'none';
            imageLabel.style.display = 'none';
            previewImage.src = '';

            bootstrapModal.show();
        }

        // Funzione per aprire modale in modalità modifica
        function openEditModal(productData) {
            formMode = 'edit';

            document.getElementById('product_id').value = productData.id;
            document.getElementById('product_name').value = productData.name;
            document.getElementById('product_price').value = productData.price;
            document.getElementById('product_description').value = productData.description;
            document.getElementById('product_category').value = productData.category;

            productImageInput.required = false;
            productForm.action = 'actions/edit_product.php';

            modalTitle.textContent = 'Modifica Prodotto';
            submitBtn.textContent = 'Salva Modifiche';

            // Mostra immagine esistente (assicurati che productData.image_url sia il percorso corretto)
            if (productData.image_url) {
                previewImage.src = productData.image_url;
                previewImage.style.display = 'block';
                imageLabel.style.display = 'block';

            } else {
                previewImage.style.display = 'none';
                imageLabel.style.display = 'none';
                previewImage.src = '';
            }

            bootstrapModal.show();
        }

        // Click su bottone aggiungi prodotto
        addButton.addEventListener('click', openAddModal);

        // Click su bottoni modifica prodotto
        editButtons.forEach(button => {
            button.addEventListener('click', function () {
                const id = this.dataset.id;

                fetch(`manage_products.php?fetch_product=${id}`)
                    .then(res => res.text())
                    .then(text => {
                        let data;
                        try {
                            data = JSON.parse(text);
                        } catch (e) {
                            alert('Risposta non valida dal server');
                            return;
                        }

                        if (data.error) {
                            alert('Errore: ' + data.error);
                            return;
                        }

                        openEditModal(data);
                    })
                    .catch(err => {
                        alert('Impossibile recuperare il prodotto.');
                        console.error(err);
                    });
            });
        });
    });

</script>