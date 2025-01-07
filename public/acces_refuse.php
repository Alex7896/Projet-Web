<?php
require_once '../vendor/autoload.php';

require_once 'connect.php';


// Configuration de Twig
$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);

// Rendre le fichier Twig
echo $twig->render('acces_refuse.twig');
