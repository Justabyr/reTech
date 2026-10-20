<?php
session_start();
// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur'])) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    header('Location: login.php');
    exit;
}
// Vérifier si l'utilisateur est un administrateur
if ($_SESSION['utilisateur']['role'] !== 'Admin') {
    // Rediriger vers la page d'accueil si l'utilisateur n'est pas un administrateur
    header('Location: index.php');
    exit;
}
//Connexion à la base
require 'includes/connexion.php';
//Réupérer les produits
$requete = $pdo->query("SELECT * FROM produit");
$produits = $requete->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>ReTech - Administration</title>
</head>
<body>
    <h1>Administration</h1>
        <a href="admin_commandes.php">Voir les commandes</a>
    <h2>Liste des produits</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Description</th>
                <th>Prix</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($produits as $produit) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($produit['id_prod']); ?></td>
                    <td><?php echo htmlspecialchars($produit['nom_prod']); ?></td>
                    <td><?php echo htmlspecialchars($produit['description_prod']); ?></td>
                    <td><?php echo number_format($produit['prix_prod'], 2, ',', ' '); ?> €</td>
                    <td><?php echo htmlspecialchars($produit['stock_prod']); ?></td>
                    <td>
                        <a href="modifier_produit.php?id=<?php echo $produit['id_prod']; ?>">Modifier</a> |
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Ajouter un nouveau produit</h2>
    <form action="ajouter_produit.php" method="post">
        <label for="nom">Nom :</label><br>
        <input type="text" id="nom" name="nom" required><br>

        <label for="description">Description :</label><br>
        <textarea id="description" name="description" rows="4" cols="50" required></textarea><br>

        <label for="prix">Prix :</label><br>
        <input type="number" id="prix" name="prix" step="0.01" min = "0" required><br>

        <label for="stock">Stock :</label><br>
        <input type="number" id="stock" name="stock" min = "0" required><br><br>

        <button type="submit">Ajouter le produit</button>
    </form>

    <a href="index.php">Retour à l'accueil</a>

</body>
</html>