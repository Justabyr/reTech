<?php
session_start();
$total = 0;
$lignes = [];
if (!empty($_SESSION['panier'])) {
    require 'includes/connexion.php';
    foreach ($_SESSION['panier'] as $id => $quantite) {
        $requete = $pdo->prepare("SELECT * FROM produit WHERE id_prod = :id");
        $requete->execute(['id' => $id ]);
        $produit = $requete->fetch();
        $sous_total = $produit['prix_prod'] * $quantite;
        $total = $total + $sous_total;
        $lignes[] = [
            'produit'    => $produit,
            'quantite'   => $quantite,
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
    <?php } else { ?>
        <table>
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Quantité</th>
                    <th>Prix unitaire</th>
                    <th>Sous-total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lignes as $ligne) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($ligne['produit']['nom_prod']); ?></td>
                        <td>
                            <form action="diminuer_panier.php" method="post" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $ligne['produit']['id_prod']; ?>">
                                <button type="submit">-</button>
                            </form>
                        <?php echo $ligne['quantite']; ?>
                            <form action="ajouter_panier.php" method="post" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $ligne['produit']['id_prod']; ?>">
                                <button type="submit">+</button>
                            </form>
                        </td>
                        <td><?php echo number_format($ligne['produit']['prix_prod'], 2, ',', ' '); ?> €</td>
                        <td><?php echo number_format($ligne['sous_total'], 2, ',', ' '); ?> €</td>
                        <td>
                            <form action="retirer_panier.php" method="post">
                                <input type="hidden" name="id" value="<?php echo $ligne['produit']['id_prod']; ?>">
                                <button type="submit">Retirer</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <p><strong>Total : <?php echo number_format($total, 2, ',', ' '); ?> €</strong></p>
        <div class="actions-panier">
            <form action="vider_panier.php" method="post">
                <button type="submit">Vider le panier</button>
            </form>
            <a class="btn" href="commande.php">Passer la commande</a>
        </div>
    <?php } ?>
    <a href="index.php">Retour au catalogue</a>
<?php require 'includes/footer.php'; ?>