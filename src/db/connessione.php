<?php
// Funzione per ottenere la connessione al database
function getDBConnection() {
    // Parametri di connessione al server MySQL
    $servername = "localhost"; // Il server di MySQL, in XAMPP è localhost
    $username = "root"; // Il nome utente di default di MySQL in XAMPP
    $password = ""; // La password di default di MySQL in XAMPP è vuota
    $dbname = "shop_db"; // Il nome del database che vuoi creare

    // Crea la connessione al server MySQL senza specificare un database
    $conn = new mysqli($servername, $username, $password);

    // Controlla la connessione
    if ($conn->connect_error) {
        die("Connessione fallita: " . $conn->connect_error);
    }

    // Verifica se il database esiste
    $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
    if ($conn->query($sql) === TRUE) {
        // Successo nella creazione del database, ora seleziona il database
        $conn->select_db($dbname);
    } else {
        echo "Errore nella creazione del database: " . $conn->error;
    }

    // Ora ritorna la connessione al database specificato
    return $conn;
}
?>
