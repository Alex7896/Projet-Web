<?php
// Inclure le fichier de connexion à la base de données
require_once 'connect.php';

// Récupérer les données du formulaire
$login = $_POST['login'];
$password = $_POST['password'];

try {
    // Préparer la requête SQL
    $stmt = $pdo->prepare("SELECT * FROM etudiant WHERE login = :login AND mdp = :password");
    $stmt->bindParam(':login', $login);
    $stmt->bindParam(':password', $password);

    // Exécuter la requête
    $stmt->execute();

    // Vérifier si un résultat est trouvé
    if ($stmt->rowCount() > 0) {
        echo "Connexion réussie. Bienvenue, $login!";
    } else {
        echo "Login ou mot de passe incorrect.";
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>