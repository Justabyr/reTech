<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: commande.php');
    exit;
}

if (!isset($_SESSION['utilisateur'])) {
    header('Location: login.php');
    exit;
}

if (empty($_SESSION['panier'])) {
    header('Location: panier.php');
    exit;
}

$adresse = trim($_POST['adresse'] ?? '');

if ($adresse === '') {
    header('Location: commande.php');
    exit;
}

require 'includes/connexion.php';

$total = 0;
$lignes = [];

$requete = $pdo->prepare("SELECT * FROM produit WHERE id_prod = :id");

foreach ($_SESSION['panier'] as $id => $quantite) {
    $requete->execute(['id' => $id]);
    $produit = $requete->fetch();

    // produit supprimé entre temps
    if ($produit === false) {
        unset($_SESSION['panier'][$id]);
        header('Location: panier.php');
        exit;
    }

    // plus assez de stock : on corrige le panier et on renvoie le client vers son panier
    if ($quantite > $produit['stock_prod']) {
        if ($produit['stock_prod'] == 0) {
            unset($_SESSION['panier'][$id]);
        } else {
            $_SESSION['panier'][$id] = $produit['stock_prod'];
        }
        header('Location: panier.php');
        exit;
    }

    $sous_total = $produit['prix_prod'] * $quantite;
    $total += $sous_total;

    $lignes[] = [
        'produit' => $produit,
        'quantite' => $quantite,
        'sous_total' => $sous_total
    ];
}

// transaction : soit tout est enregistré, soit rien
try {
    $pdo->beginTransaction();

    $requete = $pdo->prepare("
        INSERT INTO commande (total_com, id_util, adresse_livraison_com)
        VALUES (:total, :id_util, :adresse)
    ");
    $requete->execute([
        'total' => $total,
        'id_util' => $_SESSION['utilisateur']['id'],
        'adresse' => $adresse
    ]);
    $id_com = $pdo->lastInsertId();

    $requete_ligne = $pdo->prepare("
        INSERT INTO ligne_commande (id_com, id_prod, quantite, prix_unitaire)
        VALUES (:id_com, :id_prod, :quantite, :prix_unitaire)
    ");

    // le stock ne baisse que s'il est suffisant au moment de la commande
    $requete_stock = $pdo->prepare("
        UPDATE produit
        SET stock_prod = stock_prod - :qte
        WHERE id_prod = :id AND stock_prod >= :qte_min
    ");

    foreach ($lignes as $ligne) {
        $requete_ligne->execute([
            'id_com' => $id_com,
            'id_prod' => $ligne['produit']['id_prod'],
            'quantite' => $ligne['quantite'],
            'prix_unitaire' => $ligne['produit']['prix_prod']
        ]);

        $requete_stock->execute([
            'qte' => $ligne['quantite'],
            'qte_min' => $ligne['quantite'],
            'id' => $ligne['produit']['id_prod']
        ]);

        if ($requete_stock->rowCount() === 0) {
            throw new Exception("Stock insuffisant pour " . $ligne['produit']['nom_prod']);
        }
    }

    $pdo->commit();

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    header('Location: commande.php');
    exit;
}

unset($_SESSION['panier']);

header('Location: confirmation.php?id=' . $id_com);
exit;