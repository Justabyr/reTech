<?php
session_start();

$erreur = "";
$email = trim($_POST['email'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    require 'includes/connexion.php';

    $requete = $pdo->prepare("SELECT * FROM utilisateur WHERE email_util = :email");
    $requete->execute(['email' => $email]);
    $utilisateur = $requete->fetch();

    if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mdp'])) {
        // nouvel id de session à chaque connexion
        session_regenerate_id(true);

        $_SESSION['utilisateur'] = [
            'id' => $utilisateur['id_util'],
            'nom' => $utilisateur['nom_util'],
            'prenom' => $utilisateur['prenom_util'],
            'email' => $utilisateur['email_util'],
            'role' => $utilisateur['role']
        ];

        header('Location: index.php');
        exit;
    }

    // même message dans les deux cas, pour ne pas dire si l'email existe
    $erreur = "Email ou mot de passe incorrect.";
}

$titre = "Connexion";
require 'includes/header.php';
?>

<div class="carte-form">
    <h1>Connexion</h1>

    <?php if ($erreur !== '') { ?>
        <p class="erreur"><?php echo htmlspecialchars($erreur); ?></p>
    <?php } ?>

    <form action="login.php" method="post">
        <label for="email">Email :</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>

        <label for="mot_de_passe">Mot de passe :</label>
        <input type="password" id="mot_de_passe" name="mot_de_passe" required>

        <button type="submit">Se connecter</button>
    </form>

    <p>Pas encore de compte ? <a href="inscription.php">Inscrivez-vous ici</a>.</p>
</div>

<?php require 'includes/footer.php'; ?>