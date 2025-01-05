<?php
session_start();

// Détruire la session
session_unset(); // Supprime toutes les variables de session
session_destroy(); // Détruit la session en cours

// Redirection vers la page d'accueil
header("Location: index.php");
exit;
