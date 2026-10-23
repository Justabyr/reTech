<?php
session_start();

if (!isset($_POST['id'])) {
    die("ID du produit manquant.");
}

$id = (int) $_POST['id'];

// produit pas dans le panier : rien à faire
if (!isset($_SESSION['panier'][$id])) {
    header('Location: panier.php');
    exit;
}

$_SESSION['panier'][$id]--;

// à 0, on retire le produit du panier
if ($_SESSION['panier'][$id] <= 0) {
    unset($_SESSION['panier'][$id]);
}

header('Location: panier.php');
exit;