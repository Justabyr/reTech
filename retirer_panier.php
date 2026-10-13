<?php
session_start();

if (!isset($_POST['id'])) {
    die("ID du produit manquant.");
}
unset($_SESSION['panier'][$_POST['id']]);
header('Location: panier.php');
exit;