<?php
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

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>ReTech</title>
</head>
<body>
    <h1><?php echo htmlspecialchars($produit['nom_prod']); ?></h1>
    <a href="panier.php">Mon panier</a>

    
        <p>
            <?php echo htmlspecialchars($produit['nom_cat']); ?> -
            <?php echo htmlspecialchars($produit['description_prod']); ?> -
            <?php echo htmlspecialchars(number_format($produit['prix_prod'], 2, ',', ' ')); ?> € -
            <?php echo htmlspecialchars($produit['etat_prod']); ?> -
            <?php if ($produit['stock_prod'] > 0) { ?> 
                En stock : 
                <?php echo htmlspecialchars($produit['stock_prod']); ?> 
                <form action="ajouter_panier.php" method="post">
                <input type="hidden" name="id" value="<?php echo ($produit['id_prod']); ?>">
                <button type="submit">Ajouter au panier</button>
            </form>
            <?php } else { ?>
                Rupture de stock
            <?php } ?>
            
        </p>
        <p>
            <a href="index.php">Retour au catalogue</a>
        </p>

        
    

</body>
</html>