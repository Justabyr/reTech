<?php
session_start();

$erreur = "";

$nom = trim($_POST['nom'] ?? '');
$prenom = trim($_POST['prenom'] ?? '');
$email = trim($_POST['email'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $confirmation = $_POST['confirmation_mot_de_passe'] ?? '';

    if ($nom === '' || $prenom === '' || $email === '' || $mot_de_passe === '') {
        $erreur = "Tous les champs sont obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "L'adresse email n'est pas valide.";
    } elseif (strlen($mot_de_passe) < 8) {
        $erreur = "Le mot de passe doit contenir au moins 8 caractères.";
    } elseif ($mot_de_passe !== $confirmation) {
        $erreur = "Les mots de passe ne correspondent pas.";
    } else {
        require 'includes/connexion.php';

        // un email ne peut servir qu'à un seul compte
        $requete = $pdo->prepare("SELECT id_util FROM utilisateur WHERE email_util = :email");
        $requete->execute(['email' => $email]);

        if ($requete->fetch()) {
            $erreur = "Cet email est déjà utilisé.";
        } else {
            $requete = $pdo->prepare("
                INSERT INTO utilisateur (nom_util, prenom_util, email_util, mdp)
                VALUES (:nom, :prenom, :email, :mdp)
            ");
            $requete->execute([
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'mdp' => password_hash($mot_de_passe, PASSWORD_DEFAULT)
            ]);

            header('Location: login.php');
            exit;
        }
    }
}

$titre = "Inscription";
require 'includes/header.php';
?>

<div class="carte-form">
    <h1>Inscription</h1>

    <?php if ($erreur !== '') { ?>
        <p class="erreur"><?php echo htmlspecialchars($erreur); ?></p>
    <?php } ?>

    <form action="inscription.php" method="post">
        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($nom); ?>" required>

        <label for="prenom">Prénom :</label>
        <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($prenom); ?>" required>

        <label for="email">Email :</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>

        <label for="mot_de_passe">Mot de passe (8 caractères minimum) :</label>
        <input type="password" id="mot_de_passe" name="mot_de_passe" minlength="8" required>

        <label for="confirmation_mot_de_passe">Confirmation du mot de passe :</label>
        <input type="password" id="confirmation_mot_de_passe" name="confirmation_mot_de_passe" minlength="8" required>

        <button type="submit">S'inscrire</button>
    </form>

    <p>Déjà un compte ? <a href="login.php">Connectez-vous ici</a>.</p>
</div>

<?php require 'includes/footer.php'; ?>