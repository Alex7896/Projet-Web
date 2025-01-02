<?php
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

require_once '../vendor/autoload.php';
require_once 'connect.php';

// Récupérer l'identifiant de l'entreprise depuis l'URL
$num_entreprise = isset($_GET['num_entreprise']) ? intval($_GET['num_entreprise']) : 0;

if ($num_entreprise <= 0) {
    die("Entreprise invalide.");
}

// Récupérer les informations de l'entreprise et les stages associés
try {
    $queryEntreprise = "
        SELECT 
            e.raison_sociale, e.nom_contact, e.nom_resp, 
            e.rue_entreprise, e.cp_entreprise, e.ville_entreprise, 
            e.tel_entreprise, e.fax_entreprise, e.email, 
            e.observation, e.site_entreprise, e.niveau
        FROM entreprise e
        WHERE e.num_entreprise = :num_entreprise
    ";
    $stmtEntreprise = $pdo->prepare($queryEntreprise);
    $stmtEntreprise->bindParam(':num_entreprise', $num_entreprise, PDO::PARAM_INT);
    $stmtEntreprise->execute();
    $entreprise = $stmtEntreprise->fetch(PDO::FETCH_ASSOC);

    if (!$entreprise) {
        die("Aucune entreprise trouvée avec cet identifiant.");
    }

    // Récupérer les stages associés
    $queryStages = "
        SELECT 
            s.debut_stage, s.fin_stage, et.nom_etudiant, s.desc_projet
        FROM stage s
        JOIN etudiant et ON s.num_etudiant = et.num_etudiant
        WHERE s.num_entreprise = :num_entreprise
    ";
    $stmtStages = $pdo->prepare($queryStages);
    $stmtStages->bindParam(':num_entreprise', $num_entreprise, PDO::PARAM_INT);
    $stmtStages->execute();
    $stages = $stmtStages->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur lors de la récupération des données : " . $e->getMessage());
}

// Charger Twig et rendre le template
$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);
echo $twig->render('details_entreprise.twig', [
    'entreprise' => $entreprise,
    'stages' => $stages
]);
