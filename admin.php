<?php
session_start();

// page réservée aux admins
if (!isset($_SESSION['utilisateur'])) {
    header('Location: login.php');
    exit;
}

if ($_SESSION['utilisateur']['role'] !== 'Admin') {
    header('Location: index.php');
    exit;
}

require 'includes/connexion.php';

$produits = $pdo->query("
    SELECT produit.*, categorie.nom_cat
    FROM produit
    JOIN categorie ON produit.id_cat = categorie.id_cat
    ORDER BY produit.id_prod
")->fetchAll();

$categories = $pdo->query("SELECT * FROM categorie ORDER BY nom_cat")->fetchAll();

$titre = "Administration";
require 'includes/header.php';
?>

<h1>Administration</h1>

<a class="btn" href="admin_commandes.php">Voir les commandes</a>

<h2>Liste des produits</h2>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Catégorie</th>
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
                <td><?php echo htmlspecialchars($produit['nom_cat']); ?></td>
                <td><?php echo number_format($produit['prix_prod'], 2, ',', ' '); ?> €</td>
                <td>
                    <?php if ($produit['stock_prod'] > 0) : ?>
                        <?php echo htmlspecialchars($produit['stock_prod']); ?>
                    <?php else : ?>
                        <span class="rupture">Rupture</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="modifier_produit.php?id=<?php echo htmlspecialchars($produit['id_prod']); ?>">Modifier</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="carte-form">
    <h2>Ajouter un produit</h2>

    <form action="ajouter_produit.php" method="post">
        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" required>

        <label for="description">Description :</label>
        <textarea id="description" name="description" rows="4" required></textarea>

        <label for="prix">Prix (€) :</label>
        <input type="number" id="prix" name="prix" step="0.01" min="0" required>

        <label for="stock">Stock :</label>
        <input type="number" id="stock" name="stock" min="0" required>

        <label for="etat">État :</label>
        <select id="etat" name="etat" required>
            <option value="Correct">Correct</option>
            <option value="Bon état">Bon état</option>
            <option value="Très bon état">Très bon état</option>
        </select>

        <label for="categorie">Catégorie :</label>
        <select id="categorie" name="categorie" required>
            <?php foreach ($categories as $categorie) : ?>
                <option value="<?php echo htmlspecialchars($categorie['id_cat']); ?>">
                    <?php echo htmlspecialchars($categorie['nom_cat']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="image">Chemin de l'image :</label>
        <input type="text" id="image" name="image" placeholder="images/nom-du-fichier.png" required>

        <button type="submit">Ajouter le produit</button>
    </form>
</div>

<a class="retour" href="index.php">← Retour à l'accueil</a>

<?php require 'includes/footer.php'; ?>