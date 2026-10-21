<?php 
session_start();
// Vérifier si l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'Admin') {
    header('Location: login.php');
    exit;
}
// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $nom = $_POST['nom'] ?? '';
    $description = $_POST['description'] ?? '';
    $prix = $_POST['prix'] ?? '';
    $stock = $_POST['stock'] ?? '';
    $etat = $_POST['etat'] ?? '';
    $categorie = $_POST['categorie'] ?? '';
    $image = $_POST['image'] ?? '';

    // Vérifier les valeurs
    if (!is_numeric($prix) || $prix < 0 || !is_numeric($stock) || $stock < 0) {
        header('Location: admin.php');
        exit;
    }

    require 'includes/connexion.php';
    // Insérer le produit dans la base de données
    $requete = $pdo->prepare("INSERT INTO produit (nom_prod, description_prod, prix_prod, stock_prod, id_cat, etat_prod, image ) VALUES (:nom, :description, :prix, :stock, :categorie, :etat, :image)");
    $requete->execute([
        'nom' => $nom,
        'description' => $description,
        'prix' => $prix,
        'stock' => (int) $stock,
        'etat' => $etat,
        'categorie' => (int) $categorie,
        'image' => $image
    ]);

    header('Location: admin.php');
    exit;
}
header('Location: admin.php');
exit;
