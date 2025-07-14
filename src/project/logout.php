<?php
session_destroy();  // Distrugge i dati della sessione sul server
logout(); // Chiama la funzione di logout
?>

<script>localStorage.clear();</script>