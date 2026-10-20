<?php
session_start();
// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur'])) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    header('Location: login.php');
    exit;
}
//  Vérifier si le panier est vide
if (empty($_SESSION['panier'])) {
    // Rediriger vers la page de panier si le panier est vide
    header('Location: panier.php');
    exit;
}
$total = 0;
$lignes = [];
require 'includes/connexion.php';
    foreach ($_SESSION['panier'] as $id => $quantite) {
        $requete = $pdo->prepare("SELECT * FROM produit WHERE id_prod = :id");
        $requete->execute(['id' => $id ]);
        $produit = $requete->fetch();
        if ( $produit === false) {
            // Si le produit n'existe pas gérer l'erreur ici
            unset($_SESSION['panier'][$id]);
            header('Location: panier.php');
            exit;
        }
        if ($quantite > $produit['stock_prod']) {
            if ($produit['stock_prod'] == 0) {
                // Stock épuisé : on retire le produit du panier
                unset($_SESSION['panier'][$id]);
            } else {
                // Stock insuffisant : on ajuste la quantité au stock disponible
                $_SESSION['panier'][$id] = $produit['stock_prod'];
            }
            header('Location: panier.php');
            exit;
        }

        $sous_total = $produit['prix_prod'] * $quantite;
        $total = $total + $sous_total;
        $lignes[] = [
            'produit'    => $produit,
            'quantite'   => $quantite,
            'sous_total' => $sous_total
        ];
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>ReTech</title>
</head>
<body>
    <h1>Récapitulatif de ma commande</h1>
        <table>
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Quantité</th>
                    <th>Prix unitaire</th>
                    <th>Sous-total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lignes as $ligne) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($ligne['produit']['nom_prod']); ?></td>
                        <td><?php echo $ligne['quantite'] ?></td>
                        <td><?php echo number_format($ligne['produit']['prix_prod'], 2, ',', ' '); ?> €</td>
                        <td><?php echo number_format($ligne['sous_total'], 2, ',', ' '); ?> €</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <p><strong>Total : <?php echo number_format($total, 2, ',', ' '); ?> €</strong></p>
        <form action="valider_commande.php" method="post">
            <label for="adresse">Adresse de livraison :</label><br>
            <textarea id="adresse" name="adresse" rows="3" cols="40" required></textarea><br>
            <button type="submit">Valider la commande</button>
        </form>
    <a href="panier.php">Retour au panier</a>
</body>
</html>