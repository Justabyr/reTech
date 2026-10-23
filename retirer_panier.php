<?php
session_start();

$id = (int) ($_POST['id'] ?? 0);

unset($_SESSION['panier'][$id]);

header('Location: panier.php');
exit;