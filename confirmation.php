<?php
session_start();

if (!isset($_SESSION['utilisateur'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

require 'includes/connexion.php';

// la commande doit appartenir au client connecté
$requete = $pdo->prepare("SELECT * FROM commande WHERE id_com = :id_com AND id_util = :id_util");
$requete->execute([
    'id_com' => (int) $_GET['id'],
    'id_util' => $_SESSION['utilisateur']['id']
]);
$commande = $requete->fetch();

if (!$commande) {
    header('Location: index.php');
    exit;
}

$requete = $pdo->prepare("
    SELECT ligne_commande.*, produit.nom_prod
    FROM ligne_commande
    JOIN produit ON ligne_commande.id_prod = produit.id_prod
    WHERE ligne_commande.id_com = :id_com
");
$requete->execute(['id_com' => $commande['id_com']]);
$lignes = $requete->fetchAll();

$titre = "Confirmation de la commande";
require 'includes/header.php';
?>

<h1>Merci pour votre commande !</h1>

<p><strong>Numéro de commande :</strong> <?php echo htmlspecialchars($commande['id_com']); ?></p>
<p><strong>Date :</strong> <?php echo date('d/m/Y à H:i', strtotime($commande['date_com'])); ?></p>
<p><strong>Statut :</strong> <?php echo htmlspecialchars($commande['statut_com']); ?></p>
<p><strong>Adresse de livraison :</strong><br>
    <?php echo nl2br(htmlspecialchars($commande['adresse_livraison_com'])); ?>
</p>

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

<p class="prix">Total : <?php echo number_format($commande['total_com'], 2, ',', ' '); ?> €</p>

<div class="actions-panier">
    <a class="btn" href="facture.php?id=<?php echo htmlspecialchars($commande['id_com']); ?>">Voir la facture</a>
    <a class="retour" href="index.php">← Retour à l'accueil</a>
</div>

<?php require 'includes/footer.php'; ?>