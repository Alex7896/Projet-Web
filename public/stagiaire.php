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

require_once '../vendor/autoload.php';
require_once 'connect.php'; // Fichier de connexion à la base de données


try {
    // Requête pour récupérer tous les étudiants avec leurs stages (s'il y en a)
    $query = "
    SELECT 
        etudiant.num_etudiant,
        etudiant.nom_etudiant, 
        etudiant.prenom_etudiant,
        entreprise.raison_sociale AS nom_entreprise, 
        professeur.nom_prof, 
        professeur.prenom_prof
    FROM 
        etudiant
    LEFT JOIN 
        stage ON stage.num_etudiant = etudiant.num_etudiant
    LEFT JOIN 
        professeur ON stage.num_prof = professeur.num_prof
    LEFT JOIN 
        entreprise ON stage.num_entreprise = entreprise.num_entreprise
";
    $stmt = $pdo->query($query);
    $etudiants = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupérer les résultats

    $stmt = $pdo->query($query);
    $etudiants = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupérer les résultats
} catch (PDOException $e) {
    die("Erreur lors de la récupération des données : " . $e->getMessage());
}

// Charger Twig
$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);

// Passer les données au template Twig
echo $twig->render('stagiaire.twig', [
    'etudiants' => $etudiants
]);
