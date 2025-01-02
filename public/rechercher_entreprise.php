<?php
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    // Si l'utilisateur n'est pas connecté, redirigez-le vers la page de connexion
    header("Location: login.php");
    exit;
}
require_once '../vendor/autoload.php';


$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader, [
    'cache' => false,
]);


echo $twig->render('rechercher_entreprise.twig', [
    'nom' => 'Utilisateur',
]);
