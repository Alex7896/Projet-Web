<?php
// Inclure le fichier de connexion à la base de données
require_once 'connect.php';

// Récupérer les données du formulaire
$login = $_POST['login'];
$password = $_POST['password'];
$role = $_POST['role']; // Récupérer la valeur du rôle (étudiant ou enseignant)

try {
    if ($role === 'etudiant') {
        // Si le rôle est étudiant, vérifiez dans la table des étudiants
        $stmt = $pdo->prepare("SELECT * FROM etudiant WHERE login = :login AND mdp = :password");
        $stmt->bindParam(':login', $login);
        $stmt->bindParam(':password', $password);
    } elseif ($role === 'enseignant') {
        // Si le rôle est enseignant, vérifiez dans la table des enseignants
        $stmt = $pdo->prepare("SELECT * FROM professeur WHERE login = :login AND mdp = :password");
        $stmt->bindParam(':login', $login);
        $stmt->bindParam(':password', $password);
    } else {
        // Si aucun rôle valide n'est sélectionné
        echo "Veuillez sélectionner un rôle valide.";
        exit;
    }

    // Exécuter la requête
    $stmt->execute();

    // Vérifier si un résultat est trouvé
    if ($stmt->rowCount() > 0) {
        // Récupérer les informations de l'utilisateur
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Connexion réussie. Bienvenue, $login";
    } else {
        echo "Login ou mot de passe incorrect.";
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
