<?php
session_start();

if (!isset($_POST['id'])) {
    die("ID du produit manquant.");
}
$id = $_POST['id'];
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}
if (isset($_SESSION['panier'][$id])) {
    $_SESSION['panier'][$id]++;
} else {
    $_SESSION['panier'][$id] = 1;
}
header('Location: panier.php');
exit;