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

require_once '../vendor/autoload.php'; // Chargez Twig
require_once 'connect.php'; // Connexion à la base de données

try {
    // Requête pour récupérer les étudiants actifs
    $query = "SELECT num_etudiant, nom_etudiant, prenom_etudiant FROM etudiant WHERE en_activite = 1";
    $stmt = $pdo->query($query);
    $etudiants = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupère les résultats
} catch (PDOException $e) {
    die("Erreur lors de la récupération des étudiants : " . $e->getMessage());
}

try {
    // Requête pour récupérer les entreprises
    $query = "SELECT num_entreprise, raison_sociale FROM entreprise";
    $stmt = $pdo->query($query);
    $entreprises = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupérer les résultats sous forme de tableau associatif
} catch (PDOException $e) {
    die("Erreur lors de la récupération des entreprises : " . $e->getMessage());
}

try {
    // Requête pour récupérer les entreprises
    $query = "SELECT num_entreprise, raison_sociale FROM entreprise";
    $stmt = $pdo->query($query);
    $entreprises = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupérer les résultats sous forme de tableau associatif
} catch (PDOException $e) {
    die("Erreur lors de la récupération des entreprises : " . $e->getMessage());
}

try {
    // Requête pour récupérer les professeurs
    $query = "SELECT num_prof, nom_prof, prenom_prof FROM professeur";
    $stmt = $pdo->query($query);
    $professeurs = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupère les données sous forme de tableau associatif
} catch (PDOException $e) {
    die("Erreur lors de la récupération des professeurs : " . $e->getMessage());
}


// Récupérer les messages de succès ou d'erreur
$success_message = isset($_GET['success']) ? "L'ajout a été effectué avec succès." : null;
$error_message = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : null;

// Configurer Twig
$loader = new \Twig\Loader\FilesystemLoader('../templates'); // Dossier des templates
$twig = new \Twig\Environment($loader);

// Rendre le template Twig en passant les données
echo $twig->render('inscription.twig', [
    'etudiants' => $etudiants,
    'entreprises' => $entreprises,
    'professeurs' => $professeurs,
    'success_message' => $success_message,
    'error_message' => $error_message,
]);
