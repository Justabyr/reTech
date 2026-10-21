<?php
// Démarrer la session
session_start();

$erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];
    $confirmation_mot_de_passe = $_POST['confirmation_mot_de_passe'];

    // Vérifier que le mot de passe et la confirmation correspondent
    if ($mot_de_passe !== $confirmation_mot_de_passe) {
        $erreur = "Les mots de passe ne correspondent pas.";
    }
    else {
        // Hacher le mot de passe pour le stockage sécurisé
        $mot_de_passe_hache = password_hash($mot_de_passe, PASSWORD_DEFAULT);

        // Connexion à la base de données
        require 'includes/connexion.php';
        
        
        $requete = $pdo->prepare("SELECT id_util FROM utilisateur WHERE email_util = :email");
        $requete->execute(['email' => $email]);
        if ($requete->rowCount() > 0) {
            $erreur = "Cet email est déjà utilisé.";
        } 
        else {

            // Préparer et exécuter la requête d'insertion
            $requete = $pdo->prepare("INSERT INTO utilisateur (nom_util, prenom_util, email_util, mdp) VALUES (:nom, :prenom, :email, :mot_de_passe)");
            $requete->execute([
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'mot_de_passe' => $mot_de_passe_hache
            ]);

            //Rediriger vers la page de connexion après l'inscription
            header('Location: login.php');
            exit;

        }

        
        }   
    }
$titre = "Inscription";
require 'includes/header.php';
?>
    <h1>Inscription</h1>
    <?php if (!empty($erreur)) { ?>
        <p> <?php echo $erreur; ?> </p>
    <?php } ?>
    <form action="inscription.php" method="post">
        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" required><br><br>

        <label for="prenom">Prénom :</label>
        <input type="text" id="prenom" name="prenom" required><br><br>

        <label for="email">Email :</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="mot_de_passe">Mot de passe :</label>
        <input type="password" id="mot_de_passe" name="mot_de_passe" required><br><br>

        <label for="confirmation_mot_de_passe">Confirmation du mot de passe :</label>
        <input type="password" id="confirmation_mot_de_passe" name="confirmation_mot_de_passe" required><br><br>

        <input type="submit" value="S'inscrire">
    </form>
</body>
</html>