<?php
session_start();

// uniquement depuis le bouton du panier
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    unset($_SESSION['panier']);
}

header('Location: panier.php');
exit;