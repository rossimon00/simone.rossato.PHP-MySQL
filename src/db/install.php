<?php
// Attiviamo la visualizzazione degli errori per facilitare il debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Includo il file per la connessione al database
require_once 'connessione.php';

/**
 * Recupera la connessione al database.
 *
 * Questa funzione stabilisce la connessione al database utilizzando la configurazione
 * definita nel file 'connessione.php'.
 *
 * @return mysqli Oggetto di connessione al database.
 */
$conn = getDBConnection(); // Otteniamo la connessione al database

// Verifico se la connessione è riuscita
if (!$conn) {
    die("Errore di connessione al database: " . mysqli_connect_error());
}

// Creazione della tabella 'Users' se non esiste già
$sqlUsers = "CREATE TABLE IF NOT EXISTS Users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'manager', 'admin') DEFAULT 'user',
    banned BOOLEAN DEFAULT FALSE,
    nome VARCHAR(100) NOT NULL,
    cognome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    telefono VARCHAR(20) DEFAULT NULL
);
";

// Verifica dell'esito della query
if ($conn->query($sqlUsers) === TRUE) {
    echo "Tabella Users creata con successo!<br>";
} else {
    echo "Errore nella creazione della tabella Users: " . $conn->error . "<br>";
}

// Creazione della tabella 'Products' se non esiste già
$sqlProducts = "CREATE TABLE IF NOT EXISTS Products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    image_url VARCHAR(255),
    category ENUM('primi', 'antipasti', 'secondi', 'dessert') NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL
)";

// Verifica dell'esito della query
if ($conn->query($sqlProducts) === TRUE) {
    echo "Tabella Products creata con successo!<br>";
} else {
    echo "Errore nella creazione della tabella Products: " . $conn->error . "<br>";
}

// Dati dei piatti da inserire (Antipasti, Primi, Secondi, Dessert)
$antipasti = [
    ['Insalata di Polpo', '../assets/uploads/antipasti.png', 'Insalata di polpo con olive e capperi.', 9.50],
];

// Inserimento degli antipasti nel database
foreach ($antipasti as $antipasto) {
    // Preparo la query di inserimento per gli antipasti
    $stmt = $conn->prepare("INSERT INTO Products (name, image_url, category, description, price) VALUES (?, ?, 'antipasti', ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sssd", $antipasto[0], $antipasto[1], $antipasto[2], $antipasto[3]);
        $stmt->execute();  // Eseguo la query di inserimento
        $stmt->close();  // Chiudo la dichiarazione
    }
}

// Inserimento dei piatti per la categoria "primi"
$primi = [
    ['Spaghetti Carbonara', '../assets/uploads/carbonara.jpg', 'Deliziosi spaghetti con crema di uovo, pancetta croccante e pepe nero.', 12.50],
    ['Risotto ai Funghi', '../assets/uploads/risotto.jpg', 'Risotto cremoso con funghi porcini.', 14.00],
    ['Penne Arrabbiata', '../assets/uploads/arrabbiata.jpg', 'Penne piccanti con salsa di pomodoro e peperoncino.', 10.00],
    ['Tagliatelle al Ragù', '../assets/uploads/tagliatelle.jpg', 'Tradizionale ragù alla bolognese con tagliatelle fresche.', 13.50],
    ['Lasagna', '../assets/uploads/lasagna.jpg', 'Lasagna ricca e cremosa, con strati di carne, besciamella e formaggio.', 15.00]
];

foreach ($primi as $primo) {
    // Preparo la query di inserimento per i piatti della categoria 'primi'
    $stmt = $conn->prepare("INSERT INTO Products (name, image_url, category, description, price) VALUES (?, ?, 'primi', ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sssd", $primo[0], $primo[1], $primo[2], $primo[3]);
        $stmt->execute();  // Eseguo la query di inserimento
        $stmt->close();  // Chiudo la dichiarazione
    }
}

// Inserimento dei piatti per la categoria "secondi"
$secondi = [
    ['Salmone grigliato', '../assets/uploads/salmon.jpg', 'Salmone grigliato perfettamente con burro al limone.', 18.00],
    ['Bistecca alla Fiorentina', '../assets/uploads/fiorentina.jpg', 'Succulenta bistecca T-bone cotta alla perfezione, servita con contorni.', 25.00],
];

foreach ($secondi as $secondo) {
    // Preparo la query di inserimento per i piatti della categoria 'secondi'
    $stmt = $conn->prepare("INSERT INTO Products (name, image_url, category, description, price) VALUES (?, ?, 'secondi', ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sssd", $secondo[0], $secondo[1], $secondo[2], $secondo[3]);
        $stmt->execute();  // Eseguo la query di inserimento
        $stmt->close();  // Chiudo la dichiarazione
    }
}

// Inserimento dei piatti per la categoria "dessert"
$desserts = [
    ['Tiramisu', '../assets/uploads/tiramisu.jpg', 'Classico dessert italiano con mascarpone, caffè e cacao.', 7.50],
    ['Panna Cotta', '../assets/uploads/panna_cotta.jpg', 'Panna cotta liscia e cremosa con coulis di frutti di bosco.', 6.50],
];

foreach ($desserts as $dessert) {
    // Preparo la query di inserimento per i dessert
    $stmt = $conn->prepare("INSERT INTO Products (name, image_url, category, description, price) VALUES (?, ?, 'dessert', ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sssd", $dessert[0], $dessert[1], $dessert[2], $dessert[3]);
        $stmt->execute();  // Eseguo la query di inserimento
        $stmt->close();  // Chiudo la dichiarazione
    }
}

// Confermo che i prodotti sono stati inseriti con successo
echo "Prodotti inseriti con successo!<br>";

// Creazione della tabella PrenotazioniMare
$sqlPrenotazioniMare = "CREATE TABLE IF NOT EXISTS PrenotazioniMare (
    id INT AUTO_INCREMENT PRIMARY KEY,
    data DATE NOT NULL,
    num_ombrelloni INT DEFAULT 0,
    num_lettini INT DEFAULT 0,
    nome VARCHAR(100) NOT NULL,
    cognome VARCHAR(100) NOT NULL,
    telefono VARCHAR(20) NOT NULL
)";



if ($conn->query($sqlPrenotazioniMare) === TRUE) {
    echo "Tabella PrenotazioniMare creata con successo!<br>";
} else {
    echo "Errore nella creazione della tabella PrenotazioniMare: " . $conn->error . "<br>";
}
// Inserimento di prenotazioni mare di esempio
$prenotazioni = [
    ['2025-07-15', 1, 2, 'Mario', 'Rossi', '3331234567'],
    ['2025-07-16', 2, 4, 'Luca', 'Bianchi', '3459876543'],
    ['2025-07-17', 0, 1, 'Anna', 'Verdi', '3281122334']
];

foreach ($prenotazioni as $p) {
    $stmt = $conn->prepare("INSERT INTO PrenotazioniMare (data, num_ombrelloni, num_lettini, nome, cognome, telefono) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("siisss", $p[0], $p[1], $p[2], $p[3], $p[4], $p[5]);
        $stmt->execute();
        $stmt->close();
    }
}

// Creazione della tabella PrenotazioniRistorante
$sqlPrenotazioniRistorante = "CREATE TABLE IF NOT EXISTS PrenotazioniRistorante (
    id INT AUTO_INCREMENT PRIMARY KEY,
    data DATE NOT NULL,
    ora TIME NOT NULL,
    num_persone INT NOT NULL,
    nome VARCHAR(100) NOT NULL,
    cognome VARCHAR(100) NOT NULL,
    telefono VARCHAR(20) NOT NULL
)";

if ($conn->query($sqlPrenotazioniRistorante) === TRUE) {
    echo "Tabella PrenotazioniRistorante creata con successo!<br>";
} else {
    echo "Errore nella creazione della tabella PrenotazioniRistorante: " . $conn->error . "<br>";
}

// Inserimento di prenotazioni ristorante di esempio
$prenotazioniRistorante = [
    ['2025-07-15', '20:00:00', 2, 'Marco', 'Ferrari', '3339991111'],
    ['2025-07-16', '21:30:00', 4, 'Giulia', 'Russo', '3478882233'],
    ['2025-07-17', '19:45:00', 3, 'Alessandro', 'Gallo', '3205566778']
];

foreach ($prenotazioniRistorante as $p) {
    $stmt = $conn->prepare("INSERT INTO PrenotazioniRistorante (data, ora, num_persone, nome, cognome, telefono) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ssisss", $p[0], $p[1], $p[2], $p[3], $p[4], $p[5]);
        $stmt->execute();
        $stmt->close();
    }
}


echo "Prenotazioni mare inserite con successo!<br>";

// Creazione dell'utente admin solo se non esiste
$username = 'admin';  // Nome utente dell'admin
$password = 'admin_password';  // Password dell'admin
$hashed_password = password_hash($password, PASSWORD_DEFAULT);  // Password criptata per sicurezza
$role = 'admin';  // Ruolo dell'utente

// Nuovi dati obbligatori per l’admin
$nome = 'Admin';
$cognome = 'User';
$email = 'admin@example.com';
$telefono = '1234567890';

// Controllo se l'utente admin esiste già
$checkAdmin = $conn->prepare("SELECT id FROM Users WHERE username = ?");
$checkAdmin->bind_param("s", $username);
$checkAdmin->execute();
$checkAdmin->store_result();

if ($checkAdmin->num_rows === 0) {
    // Admin non esiste, lo inserisco con i nuovi campi
    $sqlAdmin = $conn->prepare("INSERT INTO Users (username, password, role, nome, cognome, email, telefono) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($sqlAdmin) {
        $sqlAdmin->bind_param("sssssss", $username, $hashed_password, $role, $nome, $cognome, $email, $telefono);
        if ($sqlAdmin->execute()) {
            echo "Utente admin creato con successo!<br>";
        } else {
            echo "Errore nella creazione dell'utente admin: " . $sqlAdmin->error . "<br>";
        }
        $sqlAdmin->close();
    } else {
        echo "Errore nella preparazione della query per l'utente admin: " . $conn->error . "<br>";
    }
} else {
    echo "L'utente admin esiste già.<br>";
}

$checkAdmin->close();


// Chiudiamo la connessione al database
$conn->close();
?>
