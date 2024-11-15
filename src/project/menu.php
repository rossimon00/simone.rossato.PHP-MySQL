<?php
function getMenu() {
    $menu = "<a href='dashboard.php'>Home</a> | <a href='products.php'>Catalogo Prodotti</a> | <a href='cart.php'>Carrello</a> ";
    if (checkRole('manager')) {
        $menu .= "| <a href='add_product.php'>Aggiungi Prodotto</a> ";
    }
    if (checkRole('admin')) {
        $menu .= "| <a href='view_users.php'>Gestisci Utenti</a> ";
    }
    $menu .= "| <a href='logout.php'>Logout</a>";
    return $menu;
}
?>
