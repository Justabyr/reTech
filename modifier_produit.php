<?php
session_start();

if (!isset($_SESSION['utilisateur'])) {
    header('Location: login.php');
    exit;
}

if ($_SESSION['utilisateur']['role'] !== 'Admin') {
    header('Location: index.php');
    exit;
}

require 'includes/connexion.php';
// Enregistrement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id    = $_POST['id'] ?? '';
    $prix  = $_POST['prix'] ?? '';
    $stock = $_POST['stock'] ?? '';

    // Vérifier les valeurs
    if (!is_numeric($prix) || $prix < 0 || !is_numeric($stock) || $stock < 0) {
        header('Location: modifier_produit.php?id=' . $id);
        exit;
    }

    $requete = $pdo->prepare("UPDATE produit SET prix_prod = :prix, stock_prod = :stock WHERE id_prod = :id");
    $requete->execute([
        'prix'  => $prix,
        'stock' => (int) $stock,
        'id'    => $id
    ]);

    header('Location: admin.php');
    exit;
}

// Récupérer l'id depuis l'URL
$id = $_GET['id'] ?? 0;

// Chercher le produit
$requete = $pdo->prepare("SELECT * FROM produit WHERE id_prod = :id");
$requete->execute(['id' => $id]);
$produit = $requete->fetch();

// Produit introuvable
if (!$produit) {
    header('Location: admin.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>ReTech - Modifier un produit</title>
</head>
<body>
    <h1>Modifier : <?php echo htmlspecialchars($produit['nom_prod']); ?></h1>

    <form action="modifier_produit.php" method="post">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($produit['id_prod']); ?>">

        <label for="prix">Prix (€) :</label><br>
        <input type="number" id="prix" name="prix" step="0.01" min="0" required value="<?php echo htmlspecialchars($produit['prix_prod']); ?>"><br>

        <label for="stock">Stock :</label><br>
        <input type="number" id="stock" name="stock" min="0" required value="<?php echo htmlspecialchars($produit['stock_prod']); ?>"><br><br>

        <button type="submit">Enregistrer</button>
    </form>

    <a href="admin.php">Retour à l'administration</a>
</body>
</html>