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

$etudiant = isset($_GET['etudiant']) ? $_GET['etudiant'] : null;
$entreprise = isset($_GET['entreprise']) ? $_GET['entreprise'] : null;
$professeur = isset($_GET['professeur']) ? $_GET['professeur'] : null;

// Récupérer les options pour les listes déroulantes
try {
    $etudiants = $pdo->query("SELECT DISTINCT nom_etudiant FROM etudiant")->fetchAll(PDO::FETCH_ASSOC);
    $entreprises = $pdo->query("SELECT DISTINCT raison_sociale FROM entreprise")->fetchAll(PDO::FETCH_ASSOC);
    $professeurs = $pdo->query("SELECT DISTINCT nom_prof FROM professeur")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur lors de la récupération des données des listes : " . $e->getMessage());
}


try {
    $query = "
        SELECT 
            e.nom_etudiant AS etudiant,
            en.raison_sociale AS entreprise,
            p.nom_prof AS professeur
        FROM 
            stage s
        JOIN 
            etudiant e ON s.num_etudiant = e.num_etudiant
        JOIN 
            entreprise en ON s.num_entreprise = en.num_entreprise
        JOIN 
            professeur p ON s.num_prof = p.num_prof
        WHERE 
            (:etudiant IS NULL OR e.nom_etudiant LIKE :etudiant)
            AND (:entreprise IS NULL OR en.raison_sociale LIKE :entreprise)
            AND (:professeur IS NULL OR p.nom_prof LIKE :professeur)
    ";

    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':etudiant', $etudiant ? "%$etudiant%" : null, PDO::PARAM_STR);
    $stmt->bindValue(':entreprise', $entreprise ? "%$entreprise%" : null, PDO::PARAM_STR);
    $stmt->bindValue(':professeur', $professeur ? "%$professeur%" : null, PDO::PARAM_STR);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur lors de la récupération des données : " . $e->getMessage());
}


// Charger Twig
$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);

echo $twig->render('rechercher_stagiaire.twig', [
    'etudiants' => $etudiants,
    'entreprises' => $entreprises,
    'professeurs' => $professeurs,
    'results' => $results ?? [] // Si aucun résultat n'est trouvé
]);
