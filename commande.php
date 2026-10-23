<?php
session_start();

if (!isset($_SESSION['utilisateur'])) {
    header('Location: login.php');
    exit;
}

if (empty($_SESSION['panier'])) {
    header('Location: panier.php');
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

$titre = "Récapitulatif de ma commande";
require 'includes/header.php';
?>

<h1>Récapitulatif de ma commande</h1>

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
                <td><?php echo htmlspecialchars($ligne['produit']['nom_prod']); ?></td>
                <td><?php echo htmlspecialchars($ligne['quantite']); ?></td>
                <td><?php echo number_format($ligne['produit']['prix_prod'], 2, ',', ' '); ?> €</td>
                <td><?php echo number_format($ligne['sous_total'], 2, ',', ' '); ?> €</td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<p class="prix">Total : <?php echo number_format($total, 2, ',', ' '); ?> €</p>

<div class="carte-form">
    <form action="valider_commande.php" method="post">
        <label for="adresse">Adresse de livraison :</label>
        <textarea id="adresse" name="adresse" rows="3" required></textarea>

        <button type="submit">Valider la commande</button>
    </form>
</div>

<a class="retour" href="panier.php">← Retour au panier</a>

<?php require 'includes/footer.php'; ?>