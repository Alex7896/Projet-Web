<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Vérifiez le rôle
if ($_SESSION['user']['role'] !== 'enseignant') {
    header("Location: acces_refuse.php");
    exit;
}

require_once '../vendor/autoload.php';
require_once 'connect.php';

// Initialisation des variables
$success_message = null;
$error_message = null;

// Récupérer l'ID du stage à modifier
$num_stage = $_GET['num_stage'] ?? null;

if (!$num_stage) {
    die("ID du stage manquant.");
}

try {
    // Récupérer les données du stage
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
        die("Stage introuvable.");
    }

    // Récupérer les étudiants, entreprises et professeurs
    $etudiants = $pdo->query("SELECT num_etudiant, nom_etudiant, prenom_etudiant FROM etudiant WHERE en_activite = 1")->fetchAll(PDO::FETCH_ASSOC);
    $entreprises = $pdo->query("SELECT num_entreprise, raison_sociale FROM entreprise")->fetchAll(PDO::FETCH_ASSOC);
    $professeurs = $pdo->query("SELECT num_prof, nom_prof, prenom_prof FROM professeur")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur lors de la récupération des données : " . $e->getMessage());
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
