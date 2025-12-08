<?php
// admin/index.php
require "check_admin.php";
require "../dbconnect.php";

// --- Récupération des statistiques principales ---

$connexion = dbconnect();
if (!$connexion) {
    die("Pb d'accès à la bdd");
}

// Nombre total d'électeurs
$stmt = $connexion->query("SELECT COUNT(*) FROM electeur");
$totalElecteurs = (int) $stmt->fetchColumn();

// Nombre total de scrutins
$stmt = $connexion->query("SELECT COUNT(*) FROM scrutin");
$totalScrutins = (int) $stmt->fetchColumn();

// Nombre de scrutins ouverts
$stmt = $connexion->query("SELECT COUNT(*) FROM scrutin WHERE etat_scrutin = 'ouvert'");
$scrutinsOuverts = (int) $stmt->fetchColumn();

// Nombre de scrutins clôturés
$stmt = $connexion->query("SELECT COUNT(*) FROM scrutin WHERE etat_scrutin = 'cloture'");
$scrutinsClotures = (int) $stmt->fetchColumn();

// Nombre de candidats (si la table existe)
$totalCandidats = 0;
try {
    $stmt = $connexion->query("SELECT COUNT(*) FROM candidat");
    $totalCandidats = (int) $stmt->fetchColumn();
} catch (PDOException $e) {
    // on laisse 0 si la table n'existe pas encore
}

// Derniers scrutins créés
$sql = "SELECT s.*, c.nom_compet
        FROM scrutin s
        JOIN competition c ON c.idcompetition = s.idcompetition
        ORDER BY s.date_ouverture DESC
        LIMIT 5";
$stmt = $connexion->query($sql);
$derniersScrutins = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard administration - GGVote</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<header class="admin-header">
    <h1>Dashboard GGVote</h1>
    <nav>
        <a href="scrutins.php">Scrutins</a>
        <a href="electeurs.php">Électeurs</a>
        <a href="candidats.php">Candidats</a>
        <a href="../index.php?disconnect=1" class="btn-logout">Déconnexion</a>
    </nav>
</header>

<main class="admin-main">

    <!-- Cartes de stats -->
    <section class="stats-grid">

        <article class="stat-card">
            <h2>Scrutins</h2>
            <p class="stat-number"><?= $totalScrutins ?></p>
            <p class="stat-detail">
                <?= $scrutinsOuverts ?> ouvert(s) · <?= $scrutinsClotures ?> clôturé(s)
            </p>
            <a href="scrutins.php" class="stat-link">Gérer les scrutins →</a>
        </article>

        <article class="stat-card">
            <h2>Électeurs</h2>
            <p class="stat-number"><?= $totalElecteurs ?></p>
            <p class="stat-detail">Comptes enregistrés</p>
            <a href="electeurs.php" class="stat-link">Gérer les électeurs →</a>
        </article>

        <article class="stat-card">
            <h2>Candidats</h2>
            <p class="stat-number"><?= $totalCandidats ?></p>
            <p class="stat-detail">Dans l’ensemble des compétitions</p>
            <a href="candidats.php" class="stat-link">Gérer les candidats →</a>
        </article>

    </section>

    <!-- Tableau des derniers scrutins -->
    <section class="panel">
        <div class="panel-header">
            <h2>Derniers scrutins créés</h2>
            <a href="scrutins.php" class="btn-small">Voir tous les scrutins</a>
        </div>

        <?php if (count($derniersScrutins) === 0): ?>
            <p>Aucun scrutin n’a encore été créé.</p>
        <?php else: ?>
            <table class="table-scrutins">
                <thead>
                    <tr>
                        <th>Nom du scrutin</th>
                        <th>Compétition</th>
                        <th>Ouverture</th>
                        <th>Clôture</th>
                        <th>État</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($derniersScrutins as $s): ?>
                    <tr>
                        <td><?= htmlspecialchars($s['nom_scrutin']) ?></td>
                        <td><?= htmlspecialchars($s['nom_compet']) ?></td>
                        <td><?= htmlspecialchars($s['date_ouverture']) ?></td>
                        <td><?= htmlspecialchars($s['date_cloture']) ?></td>
                        <td><?= htmlspecialchars($s['etat_scrutin']) ?></td>
                        <td>
                            <a href="scrutin_form.php?id=<?= $s['idscrutin'] ?>">Éditer</a> ·
                            <a href="scrutin_action.php?id=<?= $s['idscrutin'] ?>&action=ouvrir">Ouvrir</a> ·
                            <a href="scrutin_action.php?id=<?= $s['idscrutin'] ?>&action=cloturer">Clôturer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
