<?php
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    // Si l'utilisateur n'est pas connecté, redirigez-le vers la page de connexion
    header("Location: login.php");
    exit;
}


require_once '../vendor/autoload.php'; // Inclure le loader de Twig
require_once 'connect.php'; // Fichier de connexion à la base de données

try {
    // Récupérer les entreprises
    $queryEntreprises = "
SELECT entreprise.*, spec_entreprise.num_spec, specialite.libelle AS specialite_libelle
FROM entreprise
LEFT JOIN spec_entreprise ON entreprise.num_entreprise = spec_entreprise.num_entreprise
LEFT JOIN specialite ON spec_entreprise.num_spec = specialite.num_spec
";
    $stmtEntreprises = $pdo->query($queryEntreprises);
    $entreprises = $stmtEntreprises->rowCount() > 0 ? $stmtEntreprises->fetchAll(PDO::FETCH_ASSOC) : [];
} catch (PDOException $e) {
    die("Erreur lors de la récupération des données : " . $e->getMessage());
}

// Configurer Twig
$loader = new \Twig\Loader\FilesystemLoader('../templates'); // Dossier des templates Twig
$twig = new \Twig\Environment($loader);

// Rendre le template Twig avec les données
echo $twig->render('entreprise.twig', [
    'entreprises' => $entreprises,
]);
