<?php
require_once '../vendor/autoload.php'; // Inclure le loader de Twig
require_once 'connect.php'; // Fichier de connexion à la base de données

try {
    // Requête pour récupérer toutes les entreprises
    $query = "SELECT * FROM entreprise";
    $stmt = $pdo->query($query);

    // Vérifiez si des entreprises existent
    if ($stmt->rowCount() > 0) {
        $entreprises = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $entreprises = [];
    }
} catch (PDOException $e) {
    die("Erreur lors de la récupération des données : " . $e->getMessage());
}

// Configurer Twig
$loader = new \Twig\Loader\FilesystemLoader('../templates'); // Dossier des templates Twig
$twig = new \Twig\Environment($loader);

// Rendre le template Twig avec les données
echo $twig->render('entreprise.twig', ['entreprises' => $entreprises]);
