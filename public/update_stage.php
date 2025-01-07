<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'enseignant') {
    header("Location: login.php");
    exit;
}

require_once 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $num_stage = $_POST['num_stage'];
    $num_etudiant = $_POST['num_etudiant'];
    $num_entreprise = $_POST['num_entreprise'];
    $num_prof = $_POST['num_prof'];
    $debut_stage = $_POST['debut_stage'];
    $fin_stage = $_POST['fin_stage'];
    $type_stage = $_POST['type_stage'];
    $desc_projet = $_POST['desc_projet'];
    $observation_stage = $_POST['observation_stage'];

    try {
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
        $stmt->bindParam(':num_stage', $num_stage);
        $stmt->bindParam(':num_etudiant', $num_etudiant);
        $stmt->bindParam(':num_entreprise', $num_entreprise);
        $stmt->bindParam(':num_prof', $num_prof);
        $stmt->bindParam(':debut_stage', $debut_stage);
        $stmt->bindParam(':fin_stage', $fin_stage);
        $stmt->bindParam(':type_stage', $type_stage);
        $stmt->bindParam(':desc_projet', $desc_projet);
        $stmt->bindParam(':observation_stage', $observation_stage);
        $stmt->execute();

        header("Location: modifier_stage.php?success=1&num_stage=$num_stage");
        exit;
    } catch (PDOException $e) {
        header("Location: modifier_stage.php?error=" . urlencode($e->getMessage()) . "&num_stage=$num_stage");
        exit;
    }
}
