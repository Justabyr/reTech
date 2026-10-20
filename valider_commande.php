<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: commande.php');
    exit;
}
//connexion à la base
require 'includes/connexion.php';
//login
if (!isset($_SESSION['utilisateur'])) {
    header('Location: login.php');
    exit;
}
//panier vide
if (empty($_SESSION['panier'])) {
    header('Location: panier.php');
    exit;
}
$adresse = trim($_POST['adresse'] ?? '');
//adresse vide
if (empty($adresse)) {
    header('Location: commande.php');
    exit;
}
$total = 0;
$lignes = [];
    foreach ($_SESSION['panier'] as $id => $quantite) {
        $requete = $pdo->prepare("SELECT * FROM produit WHERE id_prod = :id");
        $requete->execute(['id' => $id ]);
        $produit = $requete->fetch();
        if ( $produit === false) {
            // Si le produit n'existe pas, vous pouvez gérer l'erreur ici
            unset($_SESSION['panier'][$id]);
            header('Location: panier.php'); // Rediriger vers le panier pour informer l'utilisateur
            exit;
        }
        if ($quantite > $produit['stock_prod']) {
            if ($produit['stock_prod'] == 0) {
                // Stock épuisé : on retire le produit du panier
                unset($_SESSION['panier'][$id]);
            } else {
                // Stock insuffisant : on ajuste la quantité au stock disponible
                $_SESSION['panier'][$id] = $produit['stock_prod'];
            }
            header('Location: panier.php');
            exit;
        }

        $sous_total = $produit['prix_prod'] * $quantite;
        $total = $total + $sous_total;
        $lignes[] = [
            'produit'    => $produit,
            'quantite'   => $quantite,
            'sous_total' => $sous_total
        ];
    }
try {
    $pdo->beginTransaction();

    // 1. Créer la commande
    $requete = $pdo->prepare("INSERT INTO commande (total_com, id_util, adresse_livraison_com) VALUES (:total, :id_util, :adresse)");
    $requete->execute([
        'total'   => $total,
        'id_util' => $_SESSION['utilisateur']['id'],
        'adresse' => $adresse,
    ]);
    $id_com = $pdo->lastInsertId();

    foreach ($lignes as $ligne) {
        //Ajouter la ligne de commande
        $requete = $pdo->prepare("INSERT INTO ligne_commande (id_com, id_prod, quantite, prix_unitaire) VALUES (:id_com, :id_prod, :quantite, :prix_unitaire)");
        $requete->execute([
            'id_com'       => $id_com,
            'id_prod'      => $ligne['produit']['id_prod'],
            'quantite'     => $ligne['quantite'],
            'prix_unitaire'=> $ligne['produit']['prix_prod'],
        ]);

        //Mettre à jour le stock du produit
        $requete = $pdo->prepare("UPDATE produit SET stock_prod = stock_prod - :qte WHERE id_prod = :id AND stock_prod >= :qte_min");
        $requete->execute([
            'qte' => $ligne['quantite'],
            'qte_min' => $ligne['quantite'],
            'id'       => $ligne['produit']['id_prod'],
        ]);
        if ($requete->rowCount() === 0) {
            throw new Exception("Stock insuffisant pour le produit " . $ligne['produit']['nom_prod']);
        }
    }

    $pdo->commit();

} catch (Exception $e) {
    $pdo->rollBack();
    header('Location: commande.php');
    exit;
}
unset($_SESSION['panier']);
header('Location: confirmation.php?id=' . $id_com);
exit;