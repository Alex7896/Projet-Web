<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}


require_once '../vendor/autoload.php'; // Charger Twig
require_once 'connect.php'; // Connexion à la base de données

// Initialisation des variables
$etudiants = [];
$entreprises = [];
$professeurs = [];
$success_message = null;
$error_message = null;

try {
    // Récupérer les étudiants actifs
    $query = "SELECT num_etudiant, nom_etudiant, prenom_etudiant FROM etudiant WHERE en_activite = 1";
    $stmt = $pdo->query($query);
    $etudiants = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Erreur lors de la récupération des étudiants : " . $e->getMessage();
}

try {
    // Récupérer les entreprises
    $query = "SELECT num_entreprise, raison_sociale FROM entreprise";
    $stmt = $pdo->query($query);
    $entreprises = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Erreur lors de la récupération des entreprises : " . $e->getMessage();
}

try {
    // Récupérer les professeurs
    $query = "SELECT num_prof, nom_prof, prenom_prof FROM professeur";
    $stmt = $pdo->query($query);
    $professeurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Erreur lors de la récupération des professeurs : " . $e->getMessage();
}

// Récupérer les messages de succès ou d'erreur
if (isset($_GET['success'])) {
    $success_message = "L'ajout a été effectué avec succès.";
}
if (isset($_GET['error'])) {
    $error_message = htmlspecialchars($_GET['error']);
}

// Configurer Twig
$loader = new \Twig\Loader\FilesystemLoader('../templates'); // Chemin des templates Twig
$twig = new \Twig\Environment($loader);

// Rendre le template Twig avec les données
echo $twig->render('inscription.twig', [
    'etudiants' => $etudiants,
    'entreprises' => $entreprises,
    'professeurs' => $professeurs,
    'success_message' => $success_message,
    'error_message' => $error_message,
]);
