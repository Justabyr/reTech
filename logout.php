<?php
session_start();
unset($_SESSION['utilisateur']);
// Rediriger vers la page de connexion après la déconnexion
header('Location: login.php');
exit;
