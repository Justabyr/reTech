<?php
session_start();
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
    <?php if (isset($_SESSION['utilisateur'])) {
        echo "<p>Bonjour, " . htmlspecialchars($_SESSION['utilisateur']['prenom']) . " " . htmlspecialchars($_SESSION['utilisateur']['nom']) . " (" . htmlspecialchars($_SESSION['utilisateur']['role']) . ")</p>";
        echo '<a href="logout.php">Se déconnecter</a>';
    } else {
        echo '<a href="login.php">Se connecter</a> | <a href="inscription.php">S\'inscrire</a>';
    }
    ?>
    <br>
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