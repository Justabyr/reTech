<?php
session_start();
if (!isset($_SESSION['utilisateur'])) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    header('Location: login.php');
    exit;
}
// Vérifier si l'utilisateur est un administrateur
if ($_SESSION['utilisateur']['role'] !== 'Admin') {
    // Rediriger vers la page d'accueil si l'utilisateur n'est pas un administrateur
    header('Location: index.php');
    exit;
}
//requete pour récupérer les commandes
require 'includes/connexion.php';
$requete = $pdo->query("SELECT commande.*, utilisateur.nom_util, utilisateur.prenom_util
FROM commande
JOIN utilisateur ON commande.id_util = utilisateur.id_util
ORDER BY date_com DESC");
$commandes = $requete->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>ReTech - Toutes les commandes</title>   
</head>
<body>
    <h1>Toutes les commandes</h1>
    <?php if (empty($commandes)) : ?>
        <p>Il n'y a pas encore de commandes.</p>
    <?php else : ?>
        <table>
            <thead>
                <tr>
                    <th>Numéro de commande</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Statut</th>
                    <th>Détails</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($commandes as $commande) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($commande['id_com']); ?></td>
                        <td><?php echo htmlspecialchars($commande['nom_util']); ?></td>
                        <td><?php echo htmlspecialchars($commande['prenom_util']); ?></td>
                        <td><?php echo date('d/m/Y à H:i', strtotime($commande['date_com'])); ?></td>
                        <td><?php echo number_format($commande['total_com'], 2, ',', ' '); ?> €</td>
                        <td><?php echo htmlspecialchars($commande['statut_com']); ?></td>
                        <td>
                            <a href="facture.php?id=<?php echo $commande['id_com']; ?>">Facture</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <a href="admin.php">Retour à l'administration</a>
</body>
</html>