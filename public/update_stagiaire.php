<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'connect.php';

    $num_etudiant = $_POST['num_etudiant'] ?? null;
    $nom_etudiant = $_POST['nom_etudiant'] ?? '';
    $prenom_etudiant = $_POST['prenom_etudiant'] ?? '';
    $num_classe = $_POST['num_classe'] ?? '';

    if (!$num_etudiant) {
        die("Identifiant de l'étudiant manquant.");
    }

    try {
        $query = "
            UPDATE etudiant
            SET 
                nom_etudiant = :nom_etudiant,
                prenom_etudiant = :prenom_etudiant,
                num_classe = :num_classe
            WHERE num_etudiant = :num_etudiant
        ";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':nom_etudiant', $nom_etudiant);
        $stmt->bindParam(':prenom_etudiant', $prenom_etudiant);
        $stmt->bindParam(':num_classe', $num_classe);
        $stmt->bindParam(':num_etudiant', $num_etudiant);
        $stmt->execute();

        header("Location: stagiaire.php");
        exit;
    } catch (PDOException $e) {
        die("Erreur lors de la mise à jour de l'étudiant : " . $e->getMessage());
    }
}
