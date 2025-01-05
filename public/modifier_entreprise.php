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
    $num_entreprise = isset($_POST['num_entreprise']) ? $_POST['num_entreprise'] : null;

    // Vérifier que l'ID de l'entreprise est présent
    if (!$num_entreprise) {
        die("L'identifiant de l'entreprise est manquant.");
    }

    // Récupérer les données du formulaire
    $raison_sociale = $_POST['raison_sociale'] ?? '';
    $nom_contact = $_POST['nom_contact'] ?? '';
    $nom_resp = $_POST['nom_resp'] ?? '';
    $rue = $_POST['rue'] ?? '';
    $cp = $_POST['cp'] ?? '';
    $ville = $_POST['ville'] ?? '';
    $telephone = $_POST['telephone'] ?? '';
    $fax = $_POST['fax'] ?? '';
    $email = $_POST['email'] ?? '';
    $observation = $_POST['observation'] ?? '';
    $site = $_POST['site'] ?? '';
    $niveau = $_POST['niveau'] ?? '';
    $specialite = $_POST['specialite'] ?? '';

    try {
        // Mettre à jour les informations de l'entreprise
        $query = "
            UPDATE entreprise
            SET 
                raison_sociale = :raison_sociale,
                nom_contact = :nom_contact,
                nom_resp = :nom_resp,
                rue_entreprise = :rue,
                cp_entreprise = :cp,
                ville_entreprise = :ville,
                tel_entreprise = :telephone,
                fax_entreprise = :fax,
                email = :email,
                observation = :observation,
                site_entreprise = :site,
                niveau = :niveau
            WHERE 
                num_entreprise = :num_entreprise
        ";
        $stmt = $pdo->prepare($query);

        // Lier les paramètres
        $stmt->bindParam(':raison_sociale', $raison_sociale);
        $stmt->bindParam(':nom_contact', $nom_contact);
        $stmt->bindParam(':nom_resp', $nom_resp);
        $stmt->bindParam(':rue', $rue);
        $stmt->bindParam(':cp', $cp);
        $stmt->bindParam(':ville', $ville);
        $stmt->bindParam(':telephone', $telephone);
        $stmt->bindParam(':fax', $fax);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':observation', $observation);
        $stmt->bindParam(':site', $site);
        $stmt->bindParam(':niveau', $niveau);
        $stmt->bindParam(':num_entreprise', $num_entreprise);

        // Exécuter la requête
        $stmt->execute();

        // Mettre à jour la spécialité dans la table `spec_entreprise`
        $query_specialite = "
            UPDATE spec_entreprise
            SET num_spec = (SELECT num_spec FROM specialite WHERE libelle = :specialite LIMIT 1)
            WHERE num_entreprise = :num_entreprise
        ";
        $stmt_specialite = $pdo->prepare($query_specialite);
        $stmt_specialite->bindParam(':specialite', $specialite);
        $stmt_specialite->bindParam(':num_entreprise', $num_entreprise);
        $stmt_specialite->execute();

        // Rediriger après la mise à jour
        header("Location: entreprise.php");
        exit;
    } catch (PDOException $e) {
        die("Erreur lors de la mise à jour de l'entreprise : " . $e->getMessage());
    }
}

// Charger Twig
$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);

// Récupérer l'entreprise à modifier
$num_entreprise = $_GET['num_entreprise'] ?? null;

if (!$num_entreprise) {
    die("Identifiant de l'entreprise manquant.");
}

try {
    $query = "SELECT * FROM entreprise WHERE num_entreprise = :num_entreprise";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':num_entreprise', $num_entreprise);
    $stmt->execute();
    $entreprise = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$entreprise) {
        die("Entreprise introuvable.");
    }
} catch (PDOException $e) {
    die("Erreur lors de la récupération de l'entreprise : " . $e->getMessage());
}

// Passer les données au template Twig
echo $twig->render('modifier_entreprise.twig', [
    'entreprise' => $entreprise
]);
