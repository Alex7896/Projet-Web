<?php
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    // Si l'utilisateur n'est pas connecté, redirigez-le vers la page de connexion
    header("Location: login.php");
    exit;
}
require_once '../vendor/autoload.php';

require_once 'connect.php'; // Inclure la connexion à la base de données

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données
    $raison_sociale = $_POST['raison_sociale'] ?? '';
    $nom_contact = $_POST['nom_contact'] ?? '';
    $nom_resp = $_POST['nom_resp'] ?? '';
    $rue = $_POST['rue'] ?? '';
    $cp = $_POST['cp'] ?? '';
    $ville = $_POST['ville'] ?? '';
    $tel = $_POST['tel'] ?? '';
    $fax = $_POST['fax'] ?? '';
    $email = $_POST['email'] ?? '';
    $observation = $_POST['observation'] ?? '';
    $site = $_POST['site'] ?? '';
    $niveau = $_POST['niveau'] ?? '';
    $specialite = $_POST['specialite'] ?? '';

    try {
        // Insérer les données dans la table `entreprise`
        $query = "
            INSERT INTO entreprise (
                raison_sociale, nom_contact, nom_resp, rue_entreprise, cp_entreprise, 
                ville_entreprise, tel_entreprise, fax_entreprise, email, observation, 
                site_entreprise, niveau, en_activite
            ) VALUES (
                :raison_sociale, :nom_contact, :nom_resp, :rue, :cp, 
                :ville, :tel, :fax, :email, :observation, 
                :site, :niveau, 1
            )
        ";
        $stmt = $pdo->prepare($query);

        // Lier les paramètres
        $stmt->bindParam(':raison_sociale', $raison_sociale);
        $stmt->bindParam(':nom_contact', $nom_contact);
        $stmt->bindParam(':nom_resp', $nom_resp);
        $stmt->bindParam(':rue', $rue);
        $stmt->bindParam(':cp', $cp);
        $stmt->bindParam(':ville', $ville);
        $stmt->bindParam(':tel', $tel);
        $stmt->bindParam(':fax', $fax);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':observation', $observation);
        $stmt->bindParam(':site', $site);
        $stmt->bindParam(':niveau', $niveau);

        // Exécuter la requête
        $stmt->execute();

        // Redirection après succès
        header('Location: ajouter_entreprise.php');
        exit;
    } catch (PDOException $e) {
        die('Erreur lors de l\'ajout de l\'entreprise : ' . $e->getMessage());
    }
}


// Configuration de Twig
$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);

// Rendre le fichier Twig
echo $twig->render('ajouter_entreprise.twig');
