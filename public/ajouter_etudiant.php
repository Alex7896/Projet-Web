<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Vérifiez le rôle
if ($_SESSION['user']['role'] !== 'enseignant') {
    echo "Accès refusé. Cette page est réservée aux enseignants.";
    exit;
}

require_once '../vendor/autoload.php';
require_once 'connect.php'; // Fichier de connexion à la base de données
$loader = new \Twig\Loader\FilesystemLoader('../templates'); // Dossier des templates Twig
$twig = new \Twig\Environment($loader);

echo $twig->render('stagiaire.twig', [
    'stagiaires' => $stagiaires
]);
