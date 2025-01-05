<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Connexion à la base de données
require_once 'connect.php';

$nom_etudiant = $_GET['nom_etudiant'] ?? null;
$prenom_etudiant = $_GET['prenom_etudiant'] ?? null;

if (!$nom_etudiant || !$prenom_etudiant) {
    die("Nom ou prénom de l'étudiant manquant.");
}

try {
    // Récupérer les informations de l'étudiant
    $query = "
        SELECT * 
        FROM etudiant 
        WHERE nom_etudiant = :nom_etudiant 
        AND prenom_etudiant = :prenom_etudiant
    ";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':nom_etudiant', $nom_etudiant);
    $stmt->bindParam(':prenom_etudiant', $prenom_etudiant);
    $stmt->execute();
    $etudiant = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$etudiant) {
        die("Étudiant introuvable.");
    }
} catch (PDOException $e) {
    die("Erreur lors de la récupération des informations de l'étudiant : " . $e->getMessage());
}

// Charger le template Twig
require_once '../vendor/autoload.php';
$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);

echo $twig->render('modifier_stagiaire.twig', [
    'etudiant' => $etudiant
]);
