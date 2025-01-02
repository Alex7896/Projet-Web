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
    // Récupérer l'ID de l'étudiant depuis le bouton
    $num_etudiant = isset($_POST['num_etudiant']) ? intval($_POST['num_etudiant']) : 0;
    try {
        $pdo->beginTransaction();

        // Supprimer les enregistrements dépendants dans `stage`
        $queryStage = "DELETE FROM stage WHERE num_etudiant = :num_etudiant";
        $stmtStage = $pdo->prepare($queryStage);
        $stmtStage->bindParam(':num_etudiant', $num_etudiant, PDO::PARAM_INT);
        $stmtStage->execute();

        // Supprimer l'étudiant
        $queryEtudiant = "DELETE FROM etudiant WHERE num_etudiant = :num_etudiant";
        $stmtEtudiant = $pdo->prepare($queryEtudiant);
        $stmtEtudiant->bindParam(':num_etudiant', $num_etudiant, PDO::PARAM_INT);
        $stmtEtudiant->execute();

        $pdo->commit();

        echo "L'étudiant et ses dépendances ont été supprimés avec succès.";
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "Erreur lors de la suppression : " . $e->getMessage();
    }

    if ($num_etudiant > 0) {
        try {
            // Préparer la requête SQL de suppression
            $query = "DELETE FROM etudiant WHERE num_etudiant = :num_etudiant";
            $stmt = $pdo->prepare($query);

            // Lier l'ID
            $stmt->bindParam(':num_etudiant', $num_etudiant, PDO::PARAM_INT);

            // Exécuter la requête
            $stmt->execute();

            // Redirection après suppression
            header("Location: stagiaire.php");
            exit;
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
    } else {
        echo "Identifiant étudiant non valide.";
    }
} else {
    echo "Méthode non autorisée.";
}

try {
    // Requête pour récupérer les stagiaires
    $queryStagiaires = "SELECT * FROM etudiant";
    $stmtStagiaires = $pdo->query($queryStagiaires);
    $stagiaires = $stmtStagiaires->rowCount() > 0 ? $stmtStagiaires->fetchAll(PDO::FETCH_ASSOC) : [];
} catch (PDOException $e) {
    die("Erreur lors de la récupération des données : " . $e->getMessage());
}

$loader = new \Twig\Loader\FilesystemLoader('../templates'); // Dossier des templates Twig
$twig = new \Twig\Environment($loader);

echo $twig->render('stagiaire.twig', [
    'stagiaires' => $stagiaires
]);
