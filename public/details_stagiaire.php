<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

require_once '../vendor/autoload.php';
require_once 'connect.php'; // Fichier de connexion à la base de données

$id_stagiaire = $_GET['id_stagiaire'] ?? null;

if (!$id_stagiaire) {
    die("ID du stagiaire manquant.");
}

try {
    // Requête SQL pour récupérer les informations du stagiaire
    $query = "
    SELECT 
        etudiant.nom_etudiant AS nom,
        etudiant.prenom_etudiant AS prenom,
        entreprise.raison_sociale AS entreprise,
        professeur.nom_prof AS nom_prof,
        professeur.prenom_prof AS prenom_prof,
        stage.debut_stage,
        stage.fin_stage,
        stage.type_stage,
        stage.desc_projet,
        stage.observation_stage
    FROM 
        etudiant
    LEFT JOIN 
        stage ON stage.num_etudiant = etudiant.num_etudiant
    LEFT JOIN 
        entreprise ON stage.num_entreprise = entreprise.num_entreprise
    LEFT JOIN 
        professeur ON stage.num_prof = professeur.num_prof
    WHERE 
        etudiant.num_etudiant = :id_stagiaire
";


    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id_stagiaire', $id_stagiaire);
    $stmt->execute();
    $stagiaire = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$stagiaire) {
        die("Stagiaire introuvable.");
    }
} catch (PDOException $e) {
    die("Erreur lors de la récupération des informations : " . $e->getMessage());
}

// Charger Twig
$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);

// Passer les données au template Twig
echo $twig->render('details_stagiaire.twig', [
    'stagiaire' => $stagiaire
]);
