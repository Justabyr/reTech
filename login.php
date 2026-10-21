<?php
session_start();
$erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    // Connexion à la base de données
    require 'includes/connexion.php';

    // Préparer et exécuter la requête pour récupérer l'utilisateur
    $requete = $pdo->prepare("SELECT * FROM utilisateur WHERE email_util = :email");
    $requete->execute(['email' => $email]);
    $utilisateur = $requete->fetch();

    // Vérifier si l'utilisateur existe et si le mot de passe est correct
    if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mdp'])) {
        // Authentification réussie, stocker les informations de l'utilisateur dans la session
        $_SESSION['utilisateur'] = [
            'id' => $utilisateur['id_util'],
            'nom' => $utilisateur['nom_util'],
            'prenom' => $utilisateur['prenom_util'],
            'email' => $utilisateur['email_util'],
            'role' => $utilisateur['role']
        ];

        // Rediriger vers la page d'accueil ou une autre page protégée
        header('Location: index.php');
        exit;
    } else {
        // Authentification échouée, afficher un message d'erreur
        $erreur = "Email ou mot de passe incorrect.";
    }
}
$titre = "Connexion";
require 'includes/header.php';
?>
    <h1>Connexion</h1>
    <?php if (!empty($erreur)) { ?>
        <p><?php echo htmlspecialchars($erreur); ?></p>
    <?php } ?>
    <form action="login.php" method="post">
        <label for="email">Email :</label>
        <input type="email" name="email" id="email" required><br>

        <label for="mot_de_passe">Mot de passe :</label>
        <input type="password" name="mot_de_passe" id="mot_de_passe" required><br>

        <button type="submit">Se connecter</button>
    </form>
    <p>Pas encore de compte ? <a href="inscription.php">Inscrivez-vous ici</a>.</p>     
<?php require 'includes/footer.php'; ?>

