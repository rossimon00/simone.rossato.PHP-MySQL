<?php
require_once '../includes/connection.php';

$conn = getDBConnection(); // Ottieni la connessione al database

// Creazione della tabella Users
$sqlUsers = "CREATE TABLE IF NOT EXISTS Users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'manager', 'admin') DEFAULT 'user'
)";

// Verifica che la query venga eseguita correttamente
if ($conn->query($sqlUsers) === TRUE) {
    echo "Tabella Users creata con successo!<br>";
} else {
    echo "Errore nella creazione della tabella Users: " . $conn->error . "<br>";
}

// Creazione delle altre tabelle (Products, Cart, etc.)
$sqlProducts = "CREATE TABLE IF NOT EXISTS Products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
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
    FOREIGN KEY (user_id) REFERENCES Users(id),
    FOREIGN KEY (product_id) REFERENCES Products(id)
)";

// Esegui la query per creare la tabella Cart
if ($conn->query($sqlCart) === TRUE) {
    echo "Tabella Cart creata con successo!<br>";
} else {
    echo "Errore nella creazione della tabella Cart: " . $conn->error . "<br>";
}

// Chiudere la connessione al database
$conn->close();
?>
