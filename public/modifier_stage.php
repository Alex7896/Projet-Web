<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Vérifiez le rôle
if ($_SESSION['user']['role'] !== 'enseignant') {
    // Rediriger vers la page acces_refuse.php
    header("Location: acces_refuse.php");
    exit;
}

require_once '../vendor/autoload.php'; // Charger Twig
require_once 'connect.php'; // Connexion à la base de données

// Initialisation des variables
$etudiants = [];
$entreprises = [];
$professeurs = [];
$stage = null;
$success_message = null;
$error_message = null;

// Récupérer l'ID du stage
$num_stage = $_POST['num_stage'] ?? $_GET['num_stage'] ?? null;

if (!$num_stage) {
    header("Location: stagiaire.php?error=" . urlencode("ID du stage manquant."));
    exit;
}

try {
    // Récupérer les informations du stage
    $query = "
        SELECT 
            stage.num_stage,
            stage.debut_stage,
            stage.fin_stage,
            stage.type_stage,
            stage.desc_projet,
            stage.observation_stage,
            stage.num_etudiant,
            stage.num_prof,
            stage.num_entreprise
        FROM stage
        WHERE stage.num_stage = :num_stage
    ";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':num_stage', $num_stage, PDO::PARAM_INT);
    $stmt->execute();
    $stage = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$stage) {
        header("Location: stagiaire.php?error=" . urlencode("Stage introuvable."));
        exit;
    }

    // Récupérer les étudiants actifs
    $etudiants = $pdo->query("SELECT num_etudiant, nom_etudiant, prenom_etudiant FROM etudiant WHERE en_activite = 1")->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer les entreprises
    $entreprises = $pdo->query("SELECT num_entreprise, raison_sociale FROM entreprise")->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer les professeurs
    $professeurs = $pdo->query("SELECT num_prof, nom_prof, prenom_prof FROM professeur")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Erreur lors de la récupération des données : " . $e->getMessage();
}

// Configurer Twig
$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);

// Rendre le template Twig avec les données
echo $twig->render('modifier_stage.twig', [
    'stage' => $stage,
    'etudiants' => $etudiants,
    'entreprises' => $entreprises,
    'professeurs' => $professeurs,
    'success_message' => $success_message,
    'error_message' => $error_message,
]);
