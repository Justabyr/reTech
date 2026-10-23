<?php
session_start();

if (!isset($_SESSION['utilisateur'])) {
    header('Location: login.php');
    exit;
}

require 'includes/connexion.php';

$requete = $pdo->prepare("SELECT * FROM commande WHERE id_util = :id_util ORDER BY date_com DESC");
$requete->execute(['id_util' => $_SESSION['utilisateur']['id']]);
$commandes = $requete->fetchAll();

$titre = "Mes commandes";
require 'includes/header.php';
?>

<h1>Mes commandes</h1>

<?php if (empty($commandes)) : ?>
    <p>Vous n'avez pas encore passé de commande.</p>
    <a class="btn" href="index.php#catalogue">Découvrir le catalogue</a>
<?php else : ?>
    <table>
        <thead>
            <tr>
                <th>N° commande</th>
                <th>Date</th>
                <th>Total</th>
                <th>Statut</th>
                <th>Facture</th>
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
                        <a href="facture.php?id=<?php echo htmlspecialchars($commande['id_com']); ?>">Voir la facture</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<a class="retour" href="index.php">← Retour à l'accueil</a>

<?php require 'includes/footer.php'; ?>