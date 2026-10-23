<?php
session_start();
if (!isset($_GET['id'])) {
    die("ID du produit manquant.");
}

require 'includes/connexion.php';
$requete = $pdo->prepare("SELECT produit.*, categorie.nom_cat FROM produit JOIN categorie ON produit.id_cat = categorie.id_cat WHERE produit.id_prod = :id");
$requete->execute(['id' => $_GET['id']]);
$produit = $requete->fetch();
if (!$produit) {
    die("Produit non trouvé.");
}
$titre = $produit['nom_prod'];
require 'includes/header.php';
?>
<div class="fiche-produit">

    <div class="fiche-image">
        <img src="<?php echo htmlspecialchars($produit['image']); ?>"
             alt="<?php echo htmlspecialchars($produit['nom_prod']); ?>">
    </div>

    <div class="fiche-infos">
        <p class="categorie"><?php echo htmlspecialchars($produit['nom_cat']); ?></p>
        <h1><?php echo htmlspecialchars($produit['nom_prod']); ?></h1>
        <p class="etat"><?php echo htmlspecialchars($produit['etat_prod']); ?></p>

        <p class="prix"><?php echo number_format($produit['prix_prod'], 2, ',', ' '); ?> €</p>

        <p class="description"><?php echo htmlspecialchars($produit['description_prod']); ?></p>

        <?php if ($produit['stock_prod'] > 0) { ?>
            <p class="stock">En stock : <?php echo htmlspecialchars($produit['stock_prod']); ?></p>
            <form action="ajouter_panier.php" method="post">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($produit['id_prod']); ?>">
                <button type="submit">Ajouter au panier</button>
            </form>
        <?php } else { ?>
            <p class="rupture">Rupture de stock</p>
        <?php } ?>

        <a class="retour" href="index.php">← Retour au catalogue</a>
    </div>

</div>
<?php require 'includes/footer.php'; ?>