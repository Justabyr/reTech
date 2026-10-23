<?php
session_start();

// page réservée aux admins
if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'Admin') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin.php');
    exit;
}

$nom = trim($_POST['nom'] ?? '');
$description = trim($_POST['description'] ?? '');
$prix = $_POST['prix'] ?? '';
$stock = $_POST['stock'] ?? '';
$etat = $_POST['etat'] ?? '';
$categorie = (int) ($_POST['categorie'] ?? 0);
$image = trim($_POST['image'] ?? '');

$etats_autorises = ['Correct', 'Bon état', 'Très bon état'];

// on refuse les valeurs vides ou incorrectes
if ($nom === '' || $description === '' || $image === ''
    || !is_numeric($prix) || $prix < 0
    || !ctype_digit($stock)
    || !in_array($etat, $etats_autorises)) {
    header('Location: admin.php');
    exit;
}

require 'includes/connexion.php';

$requete = $pdo->prepare("
    INSERT INTO produit (nom_prod, description_prod, prix_prod, stock_prod, etat_prod, image, id_cat)
    VALUES (:nom, :description, :prix, :stock, :etat, :image, :categorie)
");
$requete->execute([
    'nom' => $nom,
    'description' => $description,
    'prix' => $prix,
    'stock' => (int) $stock,
    'etat' => $etat,
    'image' => $image,
    'categorie' => $categorie
]);

header('Location: admin.php');
exit;