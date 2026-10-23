<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=retech;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // on n'affiche pas le détail de l'erreur à l'utilisateur
    die("Erreur de connexion à la base de données.");
}