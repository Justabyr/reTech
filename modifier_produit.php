<?php
session_start();

// page réservée aux admins
if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'Admin') {
    header('Location: login.php');
    exit;
}

require 'includes/connexion.php';

// enregistrement des modifications
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $prix = $_POST['prix'] ?? '';
    $stock = $_POST['stock'] ?? '';

    if (!is_numeric($prix) || $prix < 0 || !ctype_digit($stock)) {
        header('Location: modifier_produit.php?id=' . $id);
        exit;
    }

    $requete = $pdo->prepare("UPDATE produit SET prix_prod = :prix, stock_prod = :stock WHERE id_prod = :id");
    $requete->execute([
        'prix' => $prix,
        'stock' => (int) $stock,
        'id' => $id
    ]);

    header('Location: admin.php');
    exit;
}

$id = (int) ($_GET['id'] ?? 0);

$requete = $pdo->prepare("SELECT * FROM produit WHERE id_prod = :id");
$requete->execute(['id' => $id]);
$produit = $requete->fetch();

if (!$produit) {
    header('Location: admin.php');
    exit;
}

$titre = "Modifier le produit";
require 'includes/header.php';
?>

<div class="carte-form">
    <h1>Modifier le produit</h1>
    <p><?php echo htmlspecialchars($produit['nom_prod']); ?></p>

    <form action="modifier_produit.php" method="post">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($produit['id_prod']); ?>">

        <label for="prix">Prix (€) :</label>
        <input type="number" id="prix" name="prix" step="0.01" min="0"
               value="<?php echo htmlspecialchars($produit['prix_prod']); ?>" required>

        <label for="stock">Stock :</label>
        <input type="number" id="stock" name="stock" min="0"
               value="<?php echo htmlspecialchars($produit['stock_prod']); ?>" required>

        <button type="submit">Enregistrer</button>
    </form>
</div>

<a class="retour" href="admin.php">← Retour à l'administration</a>

<?php require 'includes/footer.php'; ?>