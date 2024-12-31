<?php

require_once '../vendor/autoload.php';


$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader, [
    'cache' => false, 
]);


echo $twig->render('formulaire2.twig', [
    'nom' => 'Utilisateur', 
]);
