<?php

require_once '../vendor/autoload.php';


$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader, [
    'cache' => false,
]);


echo $twig->render('rechercher_entreprise.twig', [
    'nom' => 'Utilisateur',
]);
