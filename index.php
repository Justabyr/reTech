<?php
session_start();
require 'includes/connexion.php';
$requete = $pdo->query("SELECT produit.*, categorie.nom_cat FROM produit JOIN categorie ON produit.id_cat = categorie.id_cat");
$produits = $requete->fetchAll();
$titre = "Accueil";
require 'includes/header.php';
?>

<h1>Bienvenue sur ReTech</h1>
<div class="catalogue">
    <?php foreach ($produits as $produit) { ?>
    
        <div class="produit">
            <p class = "categorie"><?php echo htmlspecialchars($produit['nom_cat']); ?> </p>
            <p class = "nom"><a href="produit.php?id=<?php echo ($produit['id_prod']); ?>"><?php echo htmlspecialchars($produit['nom_prod']); ?></a> </p> 
            <p class = "prix"><?php echo htmlspecialchars(number_format($produit['prix_prod'], 2, ',', ' ')); ?> € </p>
            <p class = "etat"><?php echo htmlspecialchars($produit['etat_prod']); ?> </p>
            <p class = "stock"><?php if ($produit['stock_prod'] > 0) { ?> 
                En stock : 
                <?php echo htmlspecialchars($produit['stock_prod']); ?> 
            <?php } else { ?>
                <span class = "rupture">Rupture de stock</span>
            <?php } ?>
            </p>
        </div>  
    <?php } ?>
</div>
<?php require 'includes/footer.php'; ?>