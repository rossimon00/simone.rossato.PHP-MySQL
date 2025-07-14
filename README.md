### Progetto: Gestione Ristorante Lido Azzurro - PHP-MySQL

#### Autori
- Simone Rossato - [GitHub Repository](https://github.com/rossimo00/simone.rossato.PHP-MySQL.git)

---

### Descrizione del Progetto
Questo progetto è una web app per la gestione del ristorante "Lido Azzurro", sviluppata in PHP e MySQL, con le seguenti funzionalità:

#### Funzionalità principali
1. **Gestione Ruoli**
   - **Utente (Cliente):** Può visualizzare il menu, aggiungere piatti al carrello e completare l'ordine.
   - **Admin:** Può gestire utenti e permessi.

2. **Autenticazione**
   - Sistema di login e logout con sessioni PHP per mantenere l'autenticazione attiva.

3. **Gestione Utenti (Admin)**
   - Visualizzazione e gestione utenti registrati.
   - Visualizzazione di un menu con categorie (antipasti, primi, secondi, dolci, ecc.).
   - Possibilità di aggiungere piatti al carrello e completare l'ordine.

---

### Struttura del Progetto
La struttura del progetto è organizzata come segue:

```
ristorante-lido-azzurro/
├── assets/
│   ├── css/
│   │   └── style.css           # Foglio di stile per il progetto
│   ├── icons/                  # Icone utilizzate nell'applicazione
│   ├── images/                 # Immagini relative ai piatti e al ristorante
│   └── js/
│       └── script.js           # Funzioni JavaScript per interattività
├── uploads/                    # Immagini caricate per i piatti
├── common/
│   ├── header.php              # Header comune a tutte le pagine
│   ├── navbar.php              # Barra di navigazione dinamica
│   └── footer.php              # Footer del sito
├── db/
│   ├── install.php             # Script per creare e popolare il database
│   └── connessione.php         # Connessione al database MySQL
├── project/
│   ├── add_product.php         # Form per aggiungere piatti (manager)
│   ├── auth.php                # Gestione autenticazione
│   ├── restaurant.php           # restaurant personalizzata in base al ruolo
│   ├── login.php               # Pagina di login
│   ├── logout.php              # Script per logout
│   ├── manage_products.php     # Gestione piatti (manager)
│   ├── manage_users.php        # Gestione utenti (admin)
│   ├── menu.php                # Visualizzazione menu e carrello
└── README.txt                  # Documentazione e istruzioni
```

---

### Installazione e Configurazione
1. **Database:**
   - Esegui `install.php` nella cartella `db/` per creare il database, le tabelle e popolare i dati iniziali (es. categorie e piatti demo).

2. **Modifica `connessione.php`:**
   - Inserisci le credenziali del tuo database MySQL:
     ```php
     $host = 'localhost';
     $user = 'root';
     $password = '';
     $dbname = 'ristorante';
     ```

3. **Caricamento del Progetto:**
   - Assicurati che tutti i file siano caricati in una directory accessibile dal server web, ad esempio `localhost/ristorante-lido-azzurro`.

4. **Login:**
   - Visita `login.php` per accedere con uno dei seguenti ruoli:
     - Admin (email: `admin`, password: `admin_password`)

---

### Database
#### Struttura delle Tabelle
Il database contiene le seguenti tabelle:

- **users**: Gestisce gli utenti e i loro ruoli.
  ```sql
  CREATE TABLE users (
      id INT AUTO_INCREMENT PRIMARY KEY,
      email VARCHAR(255) UNIQUE NOT NULL,
      password VARCHAR(255) NOT NULL,
      role ENUM('user', 'manager', 'admin') NOT NULL,
      banned BOOLEAN DEFAULT FALSE,
  );
  ```

- **menu_items**: Contiene i piatti del menu.
  ```sql
  CREATE TABLE menu_items (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(255) NOT NULL,
      description TEXT,
      price DECIMAL(10, 2) NOT NULL,
      category ENUM('antipasti', 'primi', 'secondi', 'dolci') NOT NULL,
      image VARCHAR(255)
  );
  ```

- **orders**: Registra gli ordini effettuati dagli utenti.
  ```sql
  CREATE TABLE orders (
      id INT AUTO_INCREMENT PRIMARY KEY,
      user_id INT NOT NULL,
      total DECIMAL(10, 2) NOT NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (user_id) REFERENCES users(id)
  );
  ```

- **order_items**: Associa gli ordini ai piatti selezionati.
  ```sql
  CREATE TABLE order_items (
      id INT AUTO_INCREMENT PRIMARY KEY,
      order_id INT NOT NULL,
      menu_item_id INT NOT NULL,
      quantity INT NOT NULL,
      FOREIGN KEY (order_id) REFERENCES orders(id),
      FOREIGN KEY (menu_item_id) REFERENCES menu_items(id)
  );
  ```

---

### Funzionalità Dettagliate
#### 1. Ruoli e Permessi
- **Utenti:** Possono navigare il menu, aggiungere piatti al carrello e completare l'ordine.
- **Admin:** Possono accedere a `manage_users.php` per gestire gli utenti registrati.

#### 2. Carrello della Spesa
- Il carrello utilizza sessioni PHP per mantenere i dati durante la navigazione.
- Gli utenti possono visualizzare i dettagli dell'ordine prima di confermarlo.

#### 3. restaurant Dinamica
- La pagina `restaurant.php` visualizza contenuti diversi in base al ruolo dell'utente (es. ordini per gli utenti, gestione per manager/admin).

---


