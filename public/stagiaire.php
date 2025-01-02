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


try {
    // Requête pour récupérer les stagiaires
    $queryStagiaires = "SELECT * FROM etudiant";
    $stmtStagiaires = $pdo->query($queryStagiaires);
    $stagiaires = $stmtStagiaires->rowCount() > 0 ? $stmtStagiaires->fetchAll(PDO::FETCH_ASSOC) : [];
} catch (PDOException $e) {
    die("Erreur lors de la récupération des données : " . $e->getMessage());
}

$loader = new \Twig\Loader\FilesystemLoader('../templates'); // Dossier des templates Twig
$twig = new \Twig\Environment($loader);

echo $twig->render('stagiaire.twig', [
    'stagiaires' => $stagiaires
]);
