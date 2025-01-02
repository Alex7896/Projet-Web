<?php
// Inclure le fichier de connexion à la base de données
require_once 'connect.php';
session_start(); // Démarrer la session

// Récupérer les données du formulaire
$login = $_POST['login'];
$password = $_POST['password'];
$role = $_POST['role']; // Récupérer la valeur du rôle (étudiant ou enseignant)

try {
    if ($role === 'etudiant') {
        $stmt = $pdo->prepare("SELECT * FROM etudiant WHERE login = :login AND mdp = :password");
        $stmt->bindParam(':login', $login);
        $stmt->bindParam(':password', $password);
    } elseif ($role === 'enseignant') {
        $stmt = $pdo->prepare("SELECT * FROM professeur WHERE login = :login AND mdp = :password");
        $stmt->bindParam(':login', $login);
        $stmt->bindParam(':password', $password);
    } else {
        echo "Veuillez sélectionner un rôle valide.";
        exit;
    }

    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Récupérer les informations de l'utilisateur
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Stocker les informations utilisateur dans la session
        $_SESSION['user'] = [
            'login' => $user['login'],
            'role' => $role
        ];

        // Rediriger vers la page d'accueil ou une autre page sécurisée
        header("Location: accueil.php");
        exit;
    } else {
        echo "Login ou mot de passe incorrect.";
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
