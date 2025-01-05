<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'connect.php'; // Connexion à la base de données

    $id_stagiaire = $_POST['id_stagiaire'] ?? null;

    if (!$id_stagiaire) {
        die("ID du stagiaire manquant.");
    }

    try {
        // Supprimer le stagiaire de la table `stage` (relation entre étudiant et entreprise/professeur)
        $query_stage = "DELETE FROM stage WHERE num_etudiant = :id_stagiaire";
        $stmt_stage = $pdo->prepare($query_stage);
        $stmt_stage->bindParam(':id_stagiaire', $id_stagiaire);
        $stmt_stage->execute();

        // Supprimer l'étudiant de la table `etudiant`
        $query_etudiant = "DELETE FROM etudiant WHERE num_etudiant = :id_stagiaire";
        $stmt_etudiant = $pdo->prepare($query_etudiant);
        $stmt_etudiant->bindParam(':id_stagiaire', $id_stagiaire);
        $stmt_etudiant->execute();

        // Redirection après suppression
        header("Location: stagiaire.php");
        exit;
    } catch (PDOException $e) {
        die("Erreur lors de la suppression du stagiaire : " . $e->getMessage());
    }
} else {
    die("Requête invalide.");
}
