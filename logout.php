<?php
session_start();

// on vide et on détruit toute la session
$_SESSION = [];
session_destroy();

header('Location: login.php');
exit;