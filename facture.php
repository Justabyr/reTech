<?php
session_start();
// Connectée ?
if (!isset($_SESSION['utilisateur'])) {
    header('Location: login.php');
    exit;
}
// Id présent ?
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}
require 'includes/connexion.php';

// Chercher la commande
if ($_SESSION['utilisateur']['role'] === 'Admin') {
    // L'admin peut voir toutes les factures
    $requete = $pdo->prepare("SELECT commande.*, utilisateur.nom_util, utilisateur.prenom_util
        FROM commande
        JOIN utilisateur ON commande.id_util = utilisateur.id_util
        WHERE commande.id_com = :id_com");
    $requete->execute(['id_com' => $_GET['id']]);
} else {
    // Un client ne voit que ses propres factures
    $requete = $pdo->prepare("SELECT commande.*, utilisateur.nom_util, utilisateur.prenom_util
        FROM commande
        JOIN utilisateur ON commande.id_util = utilisateur.id_util
        WHERE commande.id_com = :id_com AND commande.id_util = :id_util");
    $requete->execute([
        'id_com'  => $_GET['id'],
        'id_util' => $_SESSION['utilisateur']['id']
    ]);
}
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
$titre = "Facture de la commande";
require 'includes/header.php';
?>
    <h1>Facture de la commande</h1>
        <h2>Vendeur</h2>
        <p>ReTech<br>12 rue de l'Exemple, 76600 Le Havre<br>SIRET : 123 456 789 00012</p>

        <h2>Client</h2>
        <p>
            <?php echo htmlspecialchars($commande['prenom_util'] . ' ' . $commande['nom_util']); ?><br>
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

    <button class="no-print" onclick="window.print()">Imprimer / Enregistrer en PDF</button>
    <a class="no-print" href="index.php">Retour à l'accueil</a>
<?php require 'includes/footer.php'; ?>