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

$sql = "
    SELECT commande.*, utilisateur.nom_util, utilisateur.prenom_util
    FROM commande
    JOIN utilisateur ON commande.id_util = utilisateur.id_util
    WHERE commande.id_com = :id_com
";
$parametres = ['id_com' => (int) $_GET['id']];

// un client ne voit que ses factures, l'admin les voit toutes
if ($_SESSION['utilisateur']['role'] !== 'Admin') {
    $sql .= " AND commande.id_util = :id_util";
    $parametres['id_util'] = $_SESSION['utilisateur']['id'];
}

$requete = $pdo->prepare($sql);
$requete->execute($parametres);
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

// les prix en base sont TTC, on retrouve le HT avec une TVA à 20 %
$total_ttc = $commande['total_com'];
$total_ht = round($total_ttc / 1.2, 2);
$tva = $total_ttc - $total_ht;

$titre = "Facture n°" . $commande['id_com'];
require 'includes/header.php';
?>

<h1>Facture n°<?php echo htmlspecialchars($commande['id_com']); ?></h1>
<p><strong>Date :</strong> <?php echo date('d/m/Y à H:i', strtotime($commande['date_com'])); ?></p>

<h2>Vendeur</h2>
<p>
    ReTech<br>
    12 rue de l'Exemple, 76600 Le Havre<br>
    SIRET : 123 456 789 00012
</p>

<h2>Client</h2>
<p>
    <?php echo htmlspecialchars($commande['prenom_util'] . ' ' . $commande['nom_util']); ?><br>
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

<p><strong>Total HT :</strong> <?php echo number_format($total_ht, 2, ',', ' '); ?> €</p>
<p><strong>TVA (20 %) :</strong> <?php echo number_format($tva, 2, ',', ' '); ?> €</p>
<p><strong>Total TTC : <?php echo number_format($total_ttc, 2, ',', ' '); ?> €</strong></p>

<div class="actions-panier no-print">
    <button onclick="window.print()">Imprimer / Enregistrer en PDF</button>
    <a class="retour" href="index.php">← Retour à l'accueil</a>
</div>

<?php require 'includes/footer.php'; ?>