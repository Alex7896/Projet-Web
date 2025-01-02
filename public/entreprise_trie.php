<?php
require_once 'connect.php'; // Fichier de connexion à la base de données

// Récupérer les valeurs des filtres
$nom = isset($_GET['nom']) ? trim($_GET['nom']) : '';
$specialite = isset($_GET['specialite']) ? trim($_GET['specialite']) : '';
$niveau = isset($_GET['niveau']) ? trim($_GET['niveau']) : '';

// Construire la requête SQL dynamique
$query = "SELECT entreprise.raison_sociale, entreprise.nom_contact, entreprise.nom_resp, entreprise.rue_entreprise, entreprise.site_entreprise, specialite.libelle AS specialite
          FROM entreprise
          LEFT JOIN spec_entreprise ON entreprise.num_entreprise = spec_entreprise.num_entreprise
          LEFT JOIN specialite ON spec_entreprise.num_spec = specialite.num_spec
          WHERE 1=1";

// Ajouter des filtres dynamiquement
$params = [];
if (!empty($nom)) {
    $query .= " AND entreprise.raison_sociale LIKE :nom";
    $params[':nom'] = "%$nom%";
}
if (!empty($specialite)) {
    $query .= " AND specialite.libelle = :specialite";
    $params[':specialite'] = $specialite;
}
if (!empty($niveau)) {
    $query .= " AND entreprise.niveau = :niveau";
    $params[':niveau'] = $niveau;
}

// Préparer et exécuter la requête
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$entreprises = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Affichage des résultats -->
<table>
    <thead>
        <tr>
            <th>Opération</th>
            <th>Entreprise</th>
            <th>Contact</th>
            <th>Responsable</th>
            <th>Adresse</th>
            <th>Site</th>
            <th>Spécialité</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($entreprises)): ?>
            <?php foreach ($entreprises as $entreprise): ?>
                <tr>
                    <td>
                        <button>Modifier</button>
                        <button>Supprimer</button>
                    </td>
                    <td><?= htmlspecialchars($entreprise['raison_sociale']) ?></td>
                    <td><?= htmlspecialchars($entreprise['nom_contact']) ?></td>
                    <td><?= htmlspecialchars($entreprise['nom_resp']) ?></td>
                    <td><?= htmlspecialchars($entreprise['rue_entreprise']) ?></td>
                    <td><a href="<?= htmlspecialchars($entreprise['site_entreprise']) ?>" target="_blank">Visiter</a></td>
                    <td><?= htmlspecialchars($entreprise['specialite']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7">Aucune entreprise trouvée avec ces critères.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>