<?php
session_start();

// page réservée aux admins
if (!isset($_SESSION['utilisateur'])) {
    header('Location: login.php');
    exit;
}

if ($_SESSION['utilisateur']['role'] !== 'Admin') {
    header('Location: index.php');
    exit;
}

require 'includes/connexion.php';

$requete = $pdo->query("
    SELECT commande.*, utilisateur.nom_util, utilisateur.prenom_util
    FROM commande
    JOIN utilisateur ON commande.id_util = utilisateur.id_util
    ORDER BY date_com DESC
");
$commandes = $requete->fetchAll();

$titre = "Toutes les commandes";
require 'includes/header.php';
?>

<h1>Toutes les commandes</h1>

<?php if (empty($commandes)) : ?>
    <p>Il n'y a pas encore de commandes.</p>
<?php else : ?>
    <table>
        <thead>
            <tr>
                <th>N° commande</th>
                <th>Client</th>
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
                    <td><?php echo htmlspecialchars($commande['prenom_util'] . ' ' . $commande['nom_util']); ?></td>
                    <td><?php echo date('d/m/Y à H:i', strtotime($commande['date_com'])); ?></td>
                    <td><?php echo number_format($commande['total_com'], 2, ',', ' '); ?> €</td>
                    <td><?php echo htmlspecialchars($commande['statut_com']); ?></td>
                    <td>
                        <a href="facture.php?id=<?php echo htmlspecialchars($commande['id_com']); ?>">Facture</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<a class="retour" href="admin.php">← Retour à l'administration</a>

<?php require 'includes/footer.php'; ?>