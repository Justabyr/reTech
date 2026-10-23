<?php
session_start();
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
$titre = $produit['nom_prod'];
require 'includes/header.php';
?>
<div class="fiche-produit">

    <div class="fiche-image">
        <?php if (!empty($produit['modele_3d']) && str_starts_with($produit['modele_3d'], 'https://sketchfab')) { ?>
            <!-- Cas 1 : modèle 3D hébergé sur Sketchfab -->
            <iframe
                src="<?php echo htmlspecialchars($produit['modele_3d']); ?>?autostart=1&transparent=1&ui_theme=dark&ui_color=8b5cf6&ui_infos=0&ui_stop=0"
                title="<?php echo htmlspecialchars($produit['nom_prod']); ?>"
                allow="autoplay; fullscreen; xr-spatial-tracking"
                allowfullscreen>
            </iframe>

        <?php } elseif (!empty($produit['modele_3d'])) { ?>
            <!-- Cas 2 : fichier .glb sur le site -->
            <model-viewer
                src="<?php echo htmlspecialchars($produit['modele_3d']); ?>"
                alt="<?php echo htmlspecialchars($produit['nom_prod']); ?>"
                camera-controls
                auto-rotate
                shadow-intensity="1">
            </model-viewer>

        <?php } else { ?>
            <!-- Cas 3 : pas de 3D, on affiche la photo -->
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

<script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/3.5.0/model-viewer.min.js"></script>
<script type="module">
    // Attendre que model-viewer soit chargé
    customElements.whenDefined('model-viewer').then(function () {
        // Qualité minimum = 100 % (au lieu de 50 % par défaut)
        customElements.get('model-viewer').minimumRenderScale = 1;
    });
</script>

<?php require 'includes/footer.php'; ?>