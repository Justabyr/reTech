<?php
require 'includes/connexion.php';
$requete = $pdo->query("SELECT produit.*, categorie.nom_cat FROM produit JOIN categorie ON produit.id_cat = categorie.id_cat");
$produits = $requete->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>ReTech</title>
</head>
<body>
    <h1>Bienvenue sur ReTech</h1>
    <a href="panier.php">Mon panier</a>

    <?php foreach ($produits as $produit) { ?>
    
        <p>
            <?php echo htmlspecialchars($produit['nom_cat']); ?> -
            <a href="produit.php?id=<?php echo ($produit['id_prod']); ?>"><?php echo htmlspecialchars($produit['nom_prod']); ?></a> - 
            <?php echo htmlspecialchars(number_format($produit['prix_prod'], 2, ',', ' ')); ?> € -
            <?php echo htmlspecialchars($produit['etat_prod']); ?> -
            <?php if ($produit['stock_prod'] > 0) { ?> 
                En stock : 
                <?php echo htmlspecialchars($produit['stock_prod']); ?> 
            <?php } else { ?>
                Rupture de stock
            <?php } ?>
        </p>
    <?php } ?>

</body>
</html>