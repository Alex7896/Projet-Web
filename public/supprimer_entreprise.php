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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $num_entreprise = $_POST['num_entreprise'];

    try {
        // Supprimer les références dans la table `spec_entreprise`
        $query1 = "DELETE FROM spec_entreprise WHERE num_entreprise = :num_entreprise";
        $stmt1 = $pdo->prepare($query1);
        $stmt1->bindParam(':num_entreprise', $num_entreprise, PDO::PARAM_INT);
        $stmt1->execute();

        // Supprimer l'entreprise
        $query2 = "DELETE FROM entreprise WHERE num_entreprise = :num_entreprise";
        $stmt2 = $pdo->prepare($query2);
        $stmt2->bindParam(':num_entreprise', $num_entreprise, PDO::PARAM_INT);
        $stmt2->execute();

        // Redirection après la suppression
        header("Location: entreprise.php");
        exit;
    } catch (PDOException $e) {
        die("Erreur lors de la suppression de l'entreprise : " . $e->getMessage());
    }
}
