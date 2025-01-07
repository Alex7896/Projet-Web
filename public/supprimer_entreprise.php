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
        // Étape 1 : Récupérer tous les `num_stage` associés à l'entreprise
        $query_stages = "SELECT num_stage FROM stage WHERE num_entreprise = :num_entreprise";
        $stmt_stages = $pdo->prepare($query_stages);
        $stmt_stages->bindParam(':num_entreprise', $num_entreprise, PDO::PARAM_INT);
        $stmt_stages->execute();
        $stages = $stmt_stages->fetchAll(PDO::FETCH_COLUMN);

        if ($stages) {
            // Étape 2 : Supprimer les références dans la table `mission` pour chaque `num_stage`
            $query_mission = "DELETE FROM mission WHERE num_stage = :num_stage";
            $stmt_mission = $pdo->prepare($query_mission);

            foreach ($stages as $num_stage) {
                $stmt_mission->bindParam(':num_stage', $num_stage, PDO::PARAM_INT);
                $stmt_mission->execute();
            }

            // Étape 3 : Supprimer les `stages` associés à l'entreprise
            $query_stage = "DELETE FROM stage WHERE num_entreprise = :num_entreprise";
            $stmt_stage = $pdo->prepare($query_stage);
            $stmt_stage->bindParam(':num_entreprise', $num_entreprise, PDO::PARAM_INT);
            $stmt_stage->execute();
        }

        // Étape 4 : Supprimer les références dans la table `spec_entreprise`
        $query_spec = "DELETE FROM spec_entreprise WHERE num_entreprise = :num_entreprise";
        $stmt_spec = $pdo->prepare($query_spec);
        $stmt_spec->bindParam(':num_entreprise', $num_entreprise, PDO::PARAM_INT);
        $stmt_spec->execute();

        // Étape 5 : Supprimer l'entreprise
        $query_entreprise = "DELETE FROM entreprise WHERE num_entreprise = :num_entreprise";
        $stmt_entreprise = $pdo->prepare($query_entreprise);
        $stmt_entreprise->bindParam(':num_entreprise', $num_entreprise, PDO::PARAM_INT);
        $stmt_entreprise->execute();

        // Redirection après la suppression
        header("Location: entreprise.php?success=1");
        exit;
    } catch (PDOException $e) {
        // Gérer l'erreur
        header("Location: entreprise.php?error=" . urlencode("Erreur lors de la suppression de l'entreprise : " . $e->getMessage()));
        exit;
    }
} else {
    header("Location: entreprise.php?error=" . urlencode("Requête invalide."));
    exit;
}
