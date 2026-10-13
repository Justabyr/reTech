<?php
session_start();

if (!isset($_POST['id'])) {
    die("ID du produit manquant.");
}
$id = $_POST['id'];
require 'includes/connexion.php';
$requete = $pdo->prepare("SELECT * FROM produit WHERE id_prod = :id");
$requete->execute(['id' => $id]);
$produit = $requete->fetch();
if (!$produit) {
    die("Produit non trouvé.");
}
if (isset($_SESSION['panier'][$id])) {
    $deja = $_SESSION['panier'][$id];
} else {
    $deja = 0;
}

if ($deja + 1 > $produit['stock_prod']) {
    die("Stock insuffisant.");
}
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