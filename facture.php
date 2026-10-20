<?php
session_start();
//login
if (!isset($_SESSION['utilisateur'])) {
    header('Location: login.php');
    exit;
}
//récuperr l'id depuis l'URL
if (!isset($_GET['id'])) {
    //rediriger vers l'index
    header('Location: index.php');
    exit;
}
//chercher la commande avec une requête préparée
require 'includes/connexion.php';
$requete = $pdo->prepare("SELECT * FROM commande WHERE id_com = :id_com AND id_util = :id_util");
$requete->execute([
    'id_com' => $_GET['id'],
    'id_util' => $_SESSION['utilisateur']['id']
]);
$commande = $requete->fetch();
//commande non trouvée
if (!$commande) {
    header('Location: index.php');
    exit;
}
//récuperer les lignes de commande
$requete = $pdo->prepare("SELECT ligne_commande.*, produit.nom_prod
FROM ligne_commande
JOIN produit ON ligne_commande.id_prod = produit.id_prod
WHERE ligne_commande.id_com = :id_com");
$requete->execute(['id_com' => $commande['id_com']]);
$lignes = $requete->fetchAll();
$total_ttc = $commande['total_com'];
$total_ht = round($total_ttc / 1.2, 2);
$taxe = $total_ttc - $total_ht;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>ReTech</title>
    <style>
    @media print {
        button, a { display: none; }
    }
</style>
</head>
<body>
    <h1>Facture de la commande</h1>
        <h2>Vendeur</h2>
        <p>ReTech<br>12 rue de l'Exemple, 76600 Le Havre<br>SIRET : 123 456 789 00012</p>

        <h2>Client</h2>
        <p>
            <?php echo htmlspecialchars($_SESSION['utilisateur']['prenom'] . ' ' . $_SESSION['utilisateur']['nom']); ?><br>
            <?php echo nl2br(htmlspecialchars($commande['adresse_livraison_com'])); ?>
        </p>
    <p><strong>Numéro de commande :</strong> <?php echo htmlspecialchars($commande['id_com']); ?></p>
    <p><strong>Date :</strong> <?php echo date('d/m/Y à H:i', strtotime($commande['date_com'])); ?></p>
    
    <h2>Détails de la commande</h2>
    <table>
        <thead>
            <tr>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Sous-total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($lignes as $ligne) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($ligne['nom_prod']); ?></td>
                    <td><?php echo htmlspecialchars($ligne['quantite']); ?></td>
                    <td><?php echo number_format($ligne['prix_unitaire'], 2, ',', ' '); ?> €</td>
                    <td><?php echo number_format($ligne['prix_unitaire'] * $ligne['quantite'], 2, ',', ' '); ?> €</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

        <p><strong>Total HT :</strong> <?php echo number_format($total_ht, 2, ',', ' '); ?> €</p>
    <p><strong>TVA (20 %) :</strong> <?php echo number_format($taxe, 2, ',', ' '); ?> €</p>
    <p><strong>Total TTC : <?php echo number_format($total_ttc, 2, ',', ' '); ?> €</strong></p>

    <button onclick="window.print()">Imprimer / Enregistrer en PDF</button>
    <a href="index.php">Retour à l'accueil</a>
</body>
</html>