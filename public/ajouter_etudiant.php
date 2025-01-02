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
    // Récupérer les données du formulaire
    $nom = isset($_POST['nom']) ? trim($_POST['nom']) : null;
    $prenom = isset($_POST['prenom']) ? trim($_POST['prenom']) : null;
    $login = isset($_POST['login']) ? trim($_POST['login']) : null;
    $password = isset($_POST['password']) ? trim($_POST['password']) : null;
    $classe = isset($_POST['classe']) ? trim($_POST['classe']) : null;

    // Vérifier que tous les champs sont remplis
    if (!empty($nom) && !empty($prenom) && !empty($login) && !empty($password) && !empty($classe)) {
        // Mapper le nom de la classe à son numéro
        $classes = [
            "SIO1-SLAM" => 1,
            "SIO2-SLAM" => 2,
            "CG1" => 3,
            "CG2" => 4,
            "AM1" => 5,
            "AM2" => 6,
            "NRC1" => 7,
            "NRC2" => 8,
            "SN1" => 9,
            "SN2" => 10,
            "SIO1-SISR" => 11,
            "SIO2-SISR" => 12
        ];

        $num_classe = $classes[$classe] ?? null; // Récupérer le numéro de classe

        if ($num_classe) {
            try {
                // Préparer la requête SQL pour insérer les données
                $query = "INSERT INTO etudiant (nom_etudiant, prenom_etudiant, login, mdp, num_classe, en_activite) 
                VALUES (:nom, :prenom, :login, :password, :num_classe, :en_activite)";
                $stmt = $pdo->prepare($query);

                // Lier les paramètres
                $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
                $stmt->bindParam(':prenom', $prenom, PDO::PARAM_STR);
                $stmt->bindParam(':login', $login, PDO::PARAM_STR);
                $stmt->bindParam(':password', $password, PDO::PARAM_STR);
                $stmt->bindParam(':num_classe', $num_classe, PDO::PARAM_INT);
                $stmt->bindValue(':en_activite', 1, PDO::PARAM_INT); // Toujours actif
                // Exécuter la requête
                $stmt->execute();

                // Confirmation de l'ajout
                echo "Le stagiaire $nom $prenom a été ajouté avec succès.";
            } catch (PDOException $e) {
                echo "Erreur lors de l'ajout : " . $e->getMessage();
            }
        } else {
            echo "Classe invalide.";
        }
    } else {
        echo "Veuillez remplir tous les champs.";
    }
}


$loader = new \Twig\Loader\FilesystemLoader('../templates'); // Dossier des templates Twig
$twig = new \Twig\Environment($loader);

echo $twig->render('ajouter_etudiant.twig', []);
