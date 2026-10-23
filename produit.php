<?php
session_start();

require 'includes/connexion.php';

$requete = $pdo->prepare("
    SELECT produit.*, categorie.nom_cat
    FROM produit
    JOIN categorie ON produit.id_cat = categorie.id_cat
    WHERE produit.id_prod = :id
");
$requete->execute(['id' => (int) ($_GET['id'] ?? 0)]);
$produit = $requete->fetch();

if (!$produit) {
    header('Location: index.php');
    exit;
}

// type d'affichage : embed Sketchfab, fichier .glb, ou simple photo
$modele = $produit['modele_3d'] ?? '';

if ($modele !== '' && str_starts_with($modele, 'https://sketchfab')) {
    $affichage = 'sketchfab';
} elseif ($modele !== '') {
    $affichage = 'glb';
} else {
    $affichage = 'photo';
}

$titre = $produit['nom_prod'];
require 'includes/header.php';
?>

<div class="fiche-produit">

    <div class="fiche-image">
        <?php if ($affichage === 'sketchfab') { ?>
            <iframe
                src="<?php echo htmlspecialchars($modele); ?>?autostart=1&transparent=1&ui_theme=dark&ui_color=8b5cf6&ui_infos=0&ui_stop=0"
                title="<?php echo htmlspecialchars($produit['nom_prod']); ?>"
                allow="autoplay; fullscreen; xr-spatial-tracking"
                allowfullscreen>
            </iframe>
        <?php } elseif ($affichage === 'glb') { ?>
            <model-viewer
                src="<?php echo htmlspecialchars($modele); ?>"
                alt="<?php echo htmlspecialchars($produit['nom_prod']); ?>"
                camera-controls
                auto-rotate
                shadow-intensity="1">
            </model-viewer>
        <?php } else { ?>
            <img src="<?php echo htmlspecialchars($produit['image']); ?>"
                 alt="<?php echo htmlspecialchars($produit['nom_prod']); ?>">
        <?php } ?>
    </div>

    <div class="fiche-infos">
        <p class="categorie"><?php echo htmlspecialchars($produit['nom_cat']); ?></p>
        <h1><?php echo htmlspecialchars($produit['nom_prod']); ?></h1>
        <p class="etat"><?php echo htmlspecialchars($produit['etat_prod']); ?></p>

        <p class="prix"><?php echo number_format($produit['prix_prod'], 2, ',', ' '); ?> €</p>

        <p class="description"><?php echo htmlspecialchars($produit['description_prod']); ?></p>

        <?php if ($produit['stock_prod'] > 0) { ?>
            <p class="stock">En stock : <?php echo htmlspecialchars($produit['stock_prod']); ?></p>
            <form action="ajouter_panier.php" method="post">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($produit['id_prod']); ?>">
                <button type="submit">Ajouter au panier</button>
            </form>
        <?php } else { ?>
            <p class="rupture">Rupture de stock</p>
        <?php } ?>

        <a class="retour" href="index.php">← Retour au catalogue</a>
    </div>

</div>

<?php if ($affichage === 'glb') { ?>
    <script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/3.5.0/model-viewer.min.js"></script>
    <script type="module">
        // garde la qualité à 100 % quand on fait tourner le modèle
        customElements.whenDefined('model-viewer').then(function () {
            customElements.get('model-viewer').minimumRenderScale = 1;
        });
    </script>
<?php } ?>

<?php require 'includes/footer.php'; ?>