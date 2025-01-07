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
        // Récupérer les `num_stage` associés au stagiaire
        $query_select_stages = "SELECT num_stage FROM stage WHERE num_etudiant = :id_stagiaire";
        $stmt_select_stages = $pdo->prepare($query_select_stages);
        $stmt_select_stages->bindParam(':id_stagiaire', $id_stagiaire, PDO::PARAM_INT);
        $stmt_select_stages->execute();
        $stages = $stmt_select_stages->fetchAll(PDO::FETCH_COLUMN);

        // Supprimer les missions associées à ces stages
        if (!empty($stages)) {
            $query_delete_missions = "DELETE FROM mission WHERE num_stage IN (" . implode(',', $stages) . ")";
            $pdo->exec($query_delete_missions);
        }

        // Supprimer les stages associés au stagiaire
        $query_stage = "DELETE FROM stage WHERE num_etudiant = :id_stagiaire";
        $stmt_stage = $pdo->prepare($query_stage);
        $stmt_stage->bindParam(':id_stagiaire', $id_stagiaire, PDO::PARAM_INT);
        $stmt_stage->execute();

        // Supprimer l'étudiant de la table `etudiant`
        $query_etudiant = "DELETE FROM etudiant WHERE num_etudiant = :id_stagiaire";
        $stmt_etudiant = $pdo->prepare($query_etudiant);
        $stmt_etudiant->bindParam(':id_stagiaire', $id_stagiaire, PDO::PARAM_INT);
        $stmt_etudiant->execute();

        // Redirection après suppression avec un message de succès
        header("Location: stagiaire.php?success=1");
        exit;
    } catch (PDOException $e) {
        // Redirection avec un message d'erreur
        $error_message = urlencode("Erreur lors de la suppression du stagiaire : " . $e->getMessage());
        header("Location: stagiaire.php?error=$error_message");
        exit;
    }
} else {
    die("Requête invalide.");
}
