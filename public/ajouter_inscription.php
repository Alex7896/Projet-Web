<?php
require_once 'connect.php'; // Connexion à la base de données

session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    // Si l'utilisateur n'est pas connecté, redirigez-le vers la page de connexion
    header("Location: login.php");
    exit;
}


require_once '../vendor/autoload.php'; // Inclure le loader de Twig
require_once 'connect.php'; // Fichier de connexion à la base de données

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les valeurs du formulaire
    $num_entreprise = isset($_POST['entreprise']) ? intval($_POST['entreprise']) : null;
    $num_etudiant = isset($_POST['etudiant']) ? intval($_POST['etudiant']) : null;
    $num_prof = isset($_POST['professeur']) ? intval($_POST['professeur']) : null;
    $date_debut = isset($_POST['date_debut_stage']) ? $_POST['date_debut_stage'] : null;
    $date_fin = isset($_POST['date_fin_stage']) ? $_POST['date_fin_stage'] : null;
    $type_stage = isset($_POST['type_stage']) ? trim($_POST['type_stage']) : null;
    $desc_projet = isset($_POST['description_stage']) ? trim($_POST['description_stage']) : null;
    $observation_stage = isset($_POST['observation']) ? trim($_POST['observation']) : null;

    // Vérifier que tous les champs requis sont remplis
    if ($num_entreprise && $num_etudiant && $num_prof && $date_debut && $date_fin && $type_stage) {
        try {
            // Préparer la requête SQL pour insérer les données
            $query = "
                INSERT INTO stage 
                (debut_stage, fin_stage, type_stage, desc_projet, observation_stage, num_etudiant, num_prof, num_entreprise)
                VALUES 
                (:debut_stage, :fin_stage, :type_stage, :desc_projet, :observation_stage, :num_etudiant, :num_prof, :num_entreprise)
            ";
            $stmt = $pdo->prepare($query);

            // Lier les valeurs aux paramètres SQL
            $stmt->bindParam(':debut_stage', $date_debut, PDO::PARAM_STR);
            $stmt->bindParam(':fin_stage', $date_fin, PDO::PARAM_STR);
            $stmt->bindParam(':type_stage', $type_stage, PDO::PARAM_STR);
            $stmt->bindParam(':desc_projet', $desc_projet, PDO::PARAM_STR);
            $stmt->bindParam(':observation_stage', $observation_stage, PDO::PARAM_STR);
            $stmt->bindParam(':num_etudiant', $num_etudiant, PDO::PARAM_INT);
            $stmt->bindParam(':num_prof', $num_prof, PDO::PARAM_INT);
            $stmt->bindParam(':num_entreprise', $num_entreprise, PDO::PARAM_INT);

            // Exécuter la requête
            $stmt->execute();

            // Redirection avec message de succès
            header('Location: inscription.php?success=1');
            exit;
        } catch (PDOException $e) {
            die("Erreur lors de l'ajout : " . $e->getMessage());
        }
    } else {
        echo "Veuillez remplir tous les champs requis.";
    }
} else {
    echo "Méthode de requête non autorisée.";
}
