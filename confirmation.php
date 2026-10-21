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
$titre = "Confirmation de la commande";
require 'includes/header.php';
?>
    <h1>Confirmation de la commande</h1>
    <p>Merci pour votre commande ! Voici le récapitulatif :</p>
    <p><strong>Numéro de commande :</strong> <?php echo htmlspecialchars($commande['id_com']); ?></p>
    <p><strong>Date :</strong> <?php echo date('d/m/Y à H:i', strtotime($commande['date_com'])); ?></p>
    <p><strong>Total :</strong> <?php echo number_format($commande['total_com'], 2, ',', ' '); ?> €</p>
    <p><strong>Statut :</strong> <?php echo htmlspecialchars($commande['statut_com']); ?></p>
    <p><strong>Adresse de livraison :</strong> <?php echo nl2br(htmlspecialchars($commande['adresse_livraison_com'])); ?></p>

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
    <p><a href="facture.php?id=<?php echo htmlspecialchars($commande['id_com']); ?>">Télécharger la facture</a></p>
    <a href="index.php">Retour à l'accueil</a>
<?php require 'includes/footer.php'; ?>
