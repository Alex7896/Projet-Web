<?php
session_start();
require_once 'connect.php'; // Fichier de connexion à la base de données
require_once '../vendor/autoload.php'; // Charger Twig

// Récupérer les valeurs des filtres
$nom = isset($_GET['nom']) ? trim($_GET['nom']) : '';
$specialite = isset($_GET['specialite']) ? trim($_GET['specialite']) : '';
$niveau = isset($_GET['niveau']) ? trim($_GET['niveau']) : '';

// Construire la requête SQL dynamique
$query = "SELECT entreprise.raison_sociale, entreprise.nom_contact, entreprise.nom_resp, 
                 entreprise.rue_entreprise, entreprise.site_entreprise, 
                 specialite.libelle AS specialite
          FROM entreprise
          LEFT JOIN spec_entreprise ON entreprise.num_entreprise = spec_entreprise.num_entreprise
          LEFT JOIN specialite ON spec_entreprise.num_spec = specialite.num_spec
          WHERE 1=1";

// Ajouter des filtres dynamiquement
$params = [];
if (!empty($nom)) {
    $query .= " AND entreprise.raison_sociale LIKE :nom";
    $params[':nom'] = "%$nom%";
}
if (!empty($specialite)) {
    $query .= " AND specialite.libelle = :specialite";
    $params[':specialite'] = $specialite;
}
if (!empty($niveau)) {
    $query .= " AND entreprise.niveau = :niveau";
    $params[':niveau'] = $niveau;
}

// Préparer et exécuter la requête
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$entreprises = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Charger Twig
$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);

// Passer les données au fichier Twig
echo $twig->render('rechercher_entreprise.twig', [
    'entreprises' => $entreprises,
    'filters' => [
        'nom' => $nom,
        'specialite' => $specialite,
        'niveau' => $niveau
    ]
]);
