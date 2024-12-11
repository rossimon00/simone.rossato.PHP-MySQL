<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'connessione.php';

/**
 * Retrieves the database connection.
 *
 * This function establishes a connection to the database using the configuration
 * defined in the `connessione.php` file.
 *
 * @return mysqli The database connection object.
 */
$conn = getDBConnection(); // Ottieni la connessione al database

if (!$conn) {
    die("Errore di connessione al database: " . mysqli_connect_error());
}

// Creazione della tabella Users
$sqlUsers = "CREATE TABLE IF NOT EXISTS Users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'manager', 'admin') DEFAULT 'user',
    banned BOOLEAN DEFAULT FALSE
)";

// Verifica che la query venga eseguita correttamente
if ($conn->query($sqlUsers) === TRUE) {
    echo "Tabella Users creata con successo!<br>";
} else {
    echo "Errore nella creazione della tabella Users: " . $conn->error . "<br>";
}

// Creazione della tabella Products
$sqlProducts = "CREATE TABLE IF NOT EXISTS Products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    image_url VARCHAR(255),
    category ENUM('primi', 'antipasti', 'secondi', 'dessert') NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL
)";

// Esegui la query per creare la tabella Products
if ($conn->query($sqlProducts) === TRUE) {
    echo "Tabella Products creata con successo!<br>";
} else {
    echo "Errore nella creazione della tabella Products: " . $conn->error . "<br>";
}

// Creazione della tabella Cart
$sqlCart = "CREATE TABLE IF NOT EXISTS Cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES Products(id) ON DELETE CASCADE
)";

// Esegui la query per creare la tabella Cart
if ($conn->query($sqlCart) === TRUE) {
    echo "Tabella Cart creata con successo!<br>";
} else {
    echo "Errore nella creazione della tabella Cart: " . $conn->error . "<br>";
}

// Creazione di un utente admin
$username = 'admin';  // Nome utente dell'admin
$password = 'admin_password';  // Password dell'admin
$hashed_password = password_hash($password, PASSWORD_DEFAULT);  // Password hashata

$sqlAdmin = $conn->prepare("INSERT INTO Users (username, password, role) VALUES (?, ?, ?)");
if ($sqlAdmin) {
    $role = 'admin';
    $sqlAdmin->bind_param("sss", $username, $hashed_password, $role);
    if ($sqlAdmin->execute()) {
        echo "Utente admin creato con successo!<br>";
    } else {
        echo "Errore nella creazione dell'utente admin: " . $sqlAdmin->error . "<br>";
    }
    $sqlAdmin->close();
} else {
    echo "Errore nella preparazione della query per l'utente admin: " . $conn->error . "<br>";
}

// Chiudere la connessione al database
$conn->close();
?>
