<?php
session_start();

require 'includes/connexion.php';

// les produits en stock d'abord
$produits = $pdo->query("
    SELECT produit.*, categorie.nom_cat
    FROM produit
    JOIN categorie ON produit.id_cat = categorie.id_cat
    ORDER BY produit.stock_prod = 0, produit.nom_prod
")->fetchAll();

$titre = "Accueil";
require 'includes/header.php';
?>

<section class="hero">
    <p class="hero-sur-titre">Tech reconditionnée</p>
    <h1>La qualité du neuf,<br>le prix en moins.</h1>
    <p class="hero-texte">Smartphones, tablettes et ordinateurs testés, nettoyés et prêts à servir.</p>
    <a href="#catalogue" class="btn">Voir le catalogue</a>

    <div class="arguments">
        <div class="argument">
            <span class="argument-icone">✓</span>
            <p><strong>Testé en atelier</strong><br>Chaque appareil est vérifié</p>
        </div>
        <div class="argument">
            <span class="argument-icone">♻</span>
            <p><strong>Écologique</strong><br>Une seconde vie pour la tech</p>
        </div>
        <div class="argument">
            <span class="argument-icone">€</span>
            <p><strong>Prix réduits</strong><br>Jusqu'à 50 % moins cher</p>
        </div>
    </div>
</section>

<h2 id="catalogue">Nos produits</h2>

<?php if (empty($produits)) { ?>
    <p>Aucun produit pour le moment.</p>
<?php } else { ?>
    <div class="catalogue">
        <?php foreach ($produits as $produit) { ?>
            <a href="produit.php?id=<?php echo htmlspecialchars($produit['id_prod']); ?>"
               class="produit <?php if ($produit['stock_prod'] <= 0) { echo 'epuise'; } ?>">

                <div class="carte-image">
                    <img src="<?php echo htmlspecialchars($produit['image']); ?>"
                         alt="<?php echo htmlspecialchars($produit['nom_prod']); ?>"
                         loading="lazy">
                </div>

                <p class="categorie"><?php echo htmlspecialchars($produit['nom_cat']); ?></p>
                <p class="nom"><?php echo htmlspecialchars($produit['nom_prod']); ?></p>
                <p class="prix"><?php echo number_format($produit['prix_prod'], 2, ',', ' '); ?> €</p>
                <p class="etat"><?php echo htmlspecialchars($produit['etat_prod']); ?></p>

                <?php if ($produit['stock_prod'] > 0) { ?>
                    <p class="stock">En stock : <?php echo htmlspecialchars($produit['stock_prod']); ?></p>
                <?php } else { ?>
                    <p class="rupture">Rupture de stock</p>
                <?php } ?>
            </a>
        <?php } ?>
    </div>
<?php } ?>

<?php require 'includes/footer.php'; ?>