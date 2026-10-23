<?php
session_start();

$total = 0;
$lignes = [];

if (!empty($_SESSION['panier'])) {
    require 'includes/connexion.php';

    $requete = $pdo->prepare("SELECT * FROM produit WHERE id_prod = :id");

    foreach ($_SESSION['panier'] as $id => $quantite) {
        $requete->execute(['id' => $id]);
        $produit = $requete->fetch();

        // produit supprimé de la boutique entre temps
        if (!$produit) {
            unset($_SESSION['panier'][$id]);
            continue;
        }

        $sous_total = $produit['prix_prod'] * $quantite;
        $total += $sous_total;

        $lignes[] = [
            'produit' => $produit,
            'quantite' => $quantite,
            'sous_total' => $sous_total
        ];
    }
}

$titre = "Mon panier";
require 'includes/header.php';
?>

<h1>Mon panier</h1>

<?php if (empty($lignes)) { ?>
    <p>Votre panier est vide.</p>
    <a class="btn" href="index.php#catalogue">Découvrir le catalogue</a>
<?php } else { ?>
    <table>
        <thead>
            <tr>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Sous-total</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($lignes as $ligne) {
                $id = htmlspecialchars($ligne['produit']['id_prod']); ?>
                <tr>
                    <td><?php echo htmlspecialchars($ligne['produit']['nom_prod']); ?></td>
                    <td>
                        <div class="quantite">
                            <form action="diminuer_panier.php" method="post">
                                <input type="hidden" name="id" value="<?php echo $id; ?>">
                                <button type="submit">−</button>
                            </form>

                            <span><?php echo htmlspecialchars($ligne['quantite']); ?></span>

                            <form action="ajouter_panier.php" method="post">
                                <input type="hidden" name="id" value="<?php echo $id; ?>">
                                <button type="submit">+</button>
                            </form>
                        </div>
                    </td>
                    <td><?php echo number_format($ligne['produit']['prix_prod'], 2, ',', ' '); ?> €</td>
                    <td><?php echo number_format($ligne['sous_total'], 2, ',', ' '); ?> €</td>
                    <td>
                        <form action="retirer_panier.php" method="post">
                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                            <button type="submit">Retirer</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <p class="prix">Total : <?php echo number_format($total, 2, ',', ' '); ?> €</p>

    <div class="actions-panier">
        <form action="vider_panier.php" method="post">
            <button type="submit">Vider le panier</button>
        </form>
        <a class="btn" href="commande.php">Passer la commande</a>
    </div>
<?php } ?>

<a class="retour" href="index.php">← Retour au catalogue</a>

<?php require 'includes/footer.php'; ?>