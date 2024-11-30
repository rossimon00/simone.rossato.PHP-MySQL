PHP-MySQL Project - Carrello della Spesa con Gestione Ruoli

Autori
------
- Simone Rossato - [GitHub Repository](https://github.com/rossimo00/simone.rossato.PHP-MySQL.git)

Descrizione del Progetto
------------------------
Questo progetto è una web app di carrello della spesa sviluppata in PHP e MySQL, che include:
- Gestione Ruoli: il sistema riconosce tre ruoli (user, manager, admin) con funzionalità personalizzate per ciascuno.
- Autenticazione: utilizza sessioni PHP per autenticare l'utente e gestire la persistenza del login.
- Catalogo e Carrello: un catalogo di prodotti acquistabili con funzionalità di aggiunta e rimozione dal carrello.
- Funzionalità Manager e Admin:
  - I gestori possono aggiungere nuovi prodotti.
  - Gli amministratori possono gestire gli utenti (es. visualizzazione e rimozione).

Riferimenti alle Slide
----------------------
Questo progetto si basa su diversi esercizi delle slide:
- PHP-10 e PHP-11: Creazione di un sito con personalizzazione di stile e funzionalità.
- PHP-13: Modifica del carrello della spesa con persistenza tramite sessioni.
- PHP-15 e PHP-16: Gestione del database, con file dedicati per connessioni (`connection.php`) e ruoli utente (estensione del carrello della spesa).

Struttura del Progetto
----------------------
nome.cognome.PHP-MySQL/
├── db/
│   └── install.php               # Script per creare e popolare il database
├── includes/
│   ├── connection.php            # Connessione al database
│   ├── menu.php                  # Menu dinamico basato sull'autenticazione e i ruoli
│   └── auth.php                  # Gestione autenticazione e sessioni
├── public/
│   ├── index.php                 # Pagina di login
│   ├── dashboard.php             # Dashboard personalizzata
│   ├── products.php              # Catalogo prodotti
│   ├── cart.php                  # Gestione del carrello
│   ├── add_product.php           # Aggiunta prodotti (per gestori)
│   ├── view_users.php            # Gestione utenti (per admin)
│   └── logout.php                # Logout e chiusura della sessione
└── README.txt                    # Dettagli del progetto e istruzioni

Installazione e Configurazione
------------------------------
1. Database: Esegui `install.php` dal folder `db/`. Questo script creerà il database, le tabelle, e popolerà i dati iniziali.
2. Modifica `connection.php`: Inserisci le tue credenziali MySQL nel file `connection.php` nella cartella `includes/`.
3. Caricamento del progetto: Assicurati che tutti i file siano caricati in una cartella accessibile dal server web, ad esempio `localhost/nome.cognome.PHP-MySQL`.
4. Login: Vai a `index.php` per accedere come utente, gestore o amministratore.

Collaborazione con Git e GitHub
-------------------------------
Abbiamo utilizzato Git e GitHub per il controllo delle versioni e la collaborazione. Ogni membro del gruppo ha un repository GitHub dedicato:
- Nome Cognome: [GitHub Repository](https://github.com/username1/nome.cognome.PHP-MySQL)
- Nome Cognome (se in gruppo): [GitHub Repository](https://github.com/username2/nome.cognome.PHP-MySQL)

Nota: Ogni commit è stato revisionato dal gruppo per garantire che tutti conoscano ogni parte del progetto.

Consegna Finale
---------------
Carica la directory `nome.cognome.PHP-MySQL` compressa in un file `.zip` o `.tar.gz` su Classroom.
Assicurati che i link ai repository GitHub e la documentazione siano completi e corretti.
