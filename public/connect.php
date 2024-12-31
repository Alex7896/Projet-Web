<?php
// Paramètres de connexion
$host = '127.0.0.1';      // Adresse du serveur
$dbname = 'bdd_geststages'; // Nom de la base de données
$username = 'usergs';      // Nom d'utilisateur de la base de données
$password = 'mdpGS';       // Mot de passe de l'utilisateur

try {
    // Créer une instance PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Configurer PDO pour afficher les erreurs
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connexion réussie à la base de données."; // Message de succès
} catch (PDOException $e) {
    // Gérer les erreurs de connexion
    echo "Erreur de connexion : " . $e->getMessage();
}
?>