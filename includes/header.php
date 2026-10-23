<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($titre); ?></title>
    <link rel="icon" type="image/png" href="images/favicon.png">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<canvas id="fond-3d"></canvas>

<nav>
    <a href="index.php" class="logo">
        <img src="images/logo.png" alt="ReTech - Accueil">
    </a>

    <?php if (isset($_SESSION['utilisateur'])) { ?>
        <span>
            Bonjour, <?php echo htmlspecialchars($_SESSION['utilisateur']['prenom'] . ' ' . $_SESSION['utilisateur']['nom']); ?>
            (<?php echo htmlspecialchars($_SESSION['utilisateur']['role']); ?>)
        </span>
        <a href="mes_commandes.php">Mes commandes</a>

        <?php if ($_SESSION['utilisateur']['role'] === 'Admin') { ?>
            <a href="admin.php">Administration</a>
        <?php } ?>

        <a href="logout.php">Se déconnecter</a>
    <?php } else { ?>
        <a href="login.php">Se connecter</a>
        <a href="inscription.php">S'inscrire</a>
    <?php } ?>

    <a href="panier.php">Mon panier</a>
</nav>

<main>