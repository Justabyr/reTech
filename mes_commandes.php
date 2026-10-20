<?php
session_start();
if (!isset($_SESSION['utilisateur'])) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    header('Location: login.php');
    exit;
}
//requete pour récupérer les commandes de l'utilisateur connecté
require 'includes/connexion.php';
$requete = $pdo->prepare("SELECT * FROM commande WHERE id_util = :id_util ORDER BY date_com DESC");
$requete->execute(['id_util' => $_SESSION['utilisateur']['id']]);
$commandes = $requete->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>ReTech - Mes commandes</title>   
</head>
<body>
    <h1>Mes commandes</h1>
    <?php if (empty($commandes)) : ?>
        <p>Vous n'avez pas encore passé de commande.</p>
    <?php else : ?>
        <table>
            <thead>
                <tr>
                    <th>Numéro de commande</th>
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
                        <td><?php echo date('d/m/Y à H:i', strtotime($commande['date_com'])); ?></td>
                        <td><?php echo number_format($commande['total_com'], 2, ',', ' '); ?> €</td>
                        <td><?php echo htmlspecialchars($commande['statut_com']); ?></td>
                        <td>
                            <a href="confirmation.php?id=<?php echo $commande['id_com']; ?>">Voir les détails</a>
                            <a href="facture.php?id=<?php echo $commande['id_com']; ?>">Facture</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <a href="index.php">Retour à l'accueil</a>
</body>
</html>