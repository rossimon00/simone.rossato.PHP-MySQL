<?php
// Funzione per ottenere la connessione al database
function getDBConnection() {
    // Parametri di connessione al database
    $servername = "localhost"; // Il server di MySQL, in XAMPP è localhost
    $username = "root"; // Il nome utente di default di MySQL in XAMPP
    $password = ""; // La password di default di MySQL in XAMPP è vuota
    $dbname = "shop_db"; // Il nome del database che hai creato in phpMyAdmin

    // Crea la connessione
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Controlla la connessione
    if ($conn->connect_error) {
        die("Connessione fallita: " . $conn->connect_error);
    }
    return $conn;
}
?>
