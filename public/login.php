<?php
// Inclure le fichier de connexion à la base de données
require_once 'connect.php';
session_start(); // Démarrer la session

// Récupérer les données du formulaire
$login = isset($_POST['login']) ? trim($_POST['login']) : null;
$password = isset($_POST['password']) ? trim($_POST['password']) : null;
$role = isset($_POST['role']) ? trim($_POST['role']) : null;

// Vérifier si le rôle est défini
if (empty($role)) {
    // Rediriger vers la page d'index avec un message d'erreur
    header("Location: index.php?error=" . urlencode("Veuillez sélectionner un rôle."));
    exit;
}

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
        // Rediriger vers la page d'index avec un message d'erreur si le rôle est invalide
        header("Location: index.php?error=" . urlencode("Rôle invalide sélectionné."));
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
        header("Location: entreprise.php");
        exit;
    } else {
        // Rediriger vers la page d'index avec un message d'erreur si les identifiants sont incorrects
        header("Location: index.php?error=" . urlencode("Login ou mot de passe incorrect."));
        exit;
    }
} catch (PDOException $e) {
    // Rediriger vers la page d'index avec un message d'erreur en cas de problème de base de données
    header("Location: index.php?error=" . urlencode("Erreur : " . $e->getMessage()));
    exit;
}

// Code pour afficher les erreurs si elles existent (en dehors du PHP principal)
?>
<?php if (isset($_GET['error'])): ?>
    <div style="color: red; font-weight: bold; margin: 10px 0;">
        <?= htmlspecialchars($_GET['error']) ?>
    </div>
<?php endif; ?>