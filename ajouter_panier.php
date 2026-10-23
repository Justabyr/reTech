<?php
session_start();

if (!isset($_POST['id'])) {
    die("ID du produit manquant.");
}

$id = (int) $_POST['id'];

require 'includes/connexion.php';

$requete = $pdo->prepare("SELECT * FROM produit WHERE id_prod = :id");
$requete->execute(['id' => $id]);
$produit = $requete->fetch();

if (!$produit) {
    die("Produit non trouvé.");
}

// quantité déjà dans le panier
$deja = $_SESSION['panier'][$id] ?? 0;

if ($deja + 1 > $produit['stock_prod']) {
    die("Stock insuffisant.");
}

$_SESSION['panier'][$id] = $deja + 1;

header('Location: panier.php');
exit;