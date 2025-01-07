<?php
session_start();

// Vérifiez si l'utilisateur est connecté et s'il est enseignant
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'enseignant') {
    header("Location: login.php");
    exit;
}

require_once 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération et validation des données du formulaire
    $num_stage = $_POST['num_stage'] ?? null;
    $num_etudiant = $_POST['num_etudiant'] ?? null;
    $num_entreprise = $_POST['num_entreprise'] ?? null;
    $num_prof = $_POST['num_prof'] ?? null;
    $debut_stage = $_POST['debut_stage'] ?? null;
    $fin_stage = $_POST['fin_stage'] ?? null;
    $type_stage = $_POST['type_stage'] ?? null;
    $desc_projet = $_POST['desc_projet'] ?? null;
    $observation_stage = $_POST['observation_stage'] ?? null;

    // Vérifiez que tous les champs requis sont remplis
    if (!$num_stage || !$num_etudiant || !$num_entreprise || !$num_prof || !$debut_stage || !$fin_stage || !$type_stage) {
        header("Location: modifier_stage.php?error=" . urlencode("Tous les champs requis doivent être remplis.") . "&num_stage=$num_stage");
        exit;
    }

    try {
        // Préparer la requête de mise à jour
        $query = "
            UPDATE stage
            SET 
                num_etudiant = :num_etudiant,
                num_entreprise = :num_entreprise,
                num_prof = :num_prof,
                debut_stage = :debut_stage,
                fin_stage = :fin_stage,
                type_stage = :type_stage,
                desc_projet = :desc_projet,
                observation_stage = :observation_stage
            WHERE 
                num_stage = :num_stage
        ";

        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':num_stage', $num_stage, PDO::PARAM_INT);
        $stmt->bindParam(':num_etudiant', $num_etudiant, PDO::PARAM_INT);
        $stmt->bindParam(':num_entreprise', $num_entreprise, PDO::PARAM_INT);
        $stmt->bindParam(':num_prof', $num_prof, PDO::PARAM_INT);
        $stmt->bindParam(':debut_stage', $debut_stage, PDO::PARAM_STR);
        $stmt->bindParam(':fin_stage', $fin_stage, PDO::PARAM_STR);
        $stmt->bindParam(':type_stage', $type_stage, PDO::PARAM_STR);
        $stmt->bindParam(':desc_projet', $desc_projet, PDO::PARAM_STR);
        $stmt->bindParam(':observation_stage', $observation_stage, PDO::PARAM_STR);

        // Exécuter la requête
        $stmt->execute();

        // Redirection avec message de succès
        header("Location: modifier_stage.php?success=1&num_stage=$num_stage");
        exit;
    } catch (PDOException $e) {
        // Gestion des erreurs et redirection avec message d'erreur
        header("Location: modifier_stage.php?error=" . urlencode("Erreur lors de la mise à jour : " . $e->getMessage()) . "&num_stage=$num_stage");
        exit;
    }
} else {
    // Redirection si la méthode n'est pas POST
    header("Location: stagiaire.php");
    exit;
}
