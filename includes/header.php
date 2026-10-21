<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($titre); ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<nav>
    <a href="index.php">ReTech</a>
    <?php if (isset($_SESSION['utilisateur'])) {
        echo "<p>Bonjour, " . htmlspecialchars($_SESSION['utilisateur']['prenom']) . " " . htmlspecialchars($_SESSION['utilisateur']['nom']) . " (" . htmlspecialchars($_SESSION['utilisateur']['role']) . ")</p>";
        echo '<a href="logout.php">Se déconnecter</a>';
        echo ' <a href="mes_commandes.php">Mes commandes</a>';
        if ($_SESSION['utilisateur']['role'] === 'Admin') {
            echo ' | <a href="admin.php">Administration</a>';
        }
    } else {
        echo '<a href="login.php">Se connecter</a> | <a href="inscription.php">S\'inscrire</a>';
    }
    ?>
    <br>
    <a href="panier.php">Mon panier</a>
</nav>
