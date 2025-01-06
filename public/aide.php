<?php
require_once '../vendor/autoload.php';

require_once 'connect.php'; // Inclure la connexion à la base de données


// Configuration de Twig
$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);

// Rendre le fichier Twig
echo $twig->render('aide.twig');
