<?php
require "check_admin.php";
require "../dbconnect.php";

$connexion = dbconnect();
if (!$connexion) {
    die("Pb d'accès à la bdd");
}

$sql = "SELECT s.*, c.nom_compet
        FROM scrutin s
        JOIN competition c ON c.idcompetition = s.idcompetition
        ORDER BY s.date_ouverture DESC";

$stmt = $connexion->query($sql);
$scrutins = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des scrutins - GGVote</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<header class="admin-header">
    <h1>Gestion des scrutins</h1>
    <nav>
        <a href="index.php">Dashboard</a>
        <a href="scrutins.php">Scrutins</a>
        <a href="electeurs.php">Électeurs</a>
        <a href="candidats.php">Candidats</a>
        <a href="resultats_calcul.php">Calculer résultats</a>
        <a href="../index.php?disconnect=1" class="btn-logout">Déconnexion</a>
    </nav>
</header>

<main class="admin-main">

    <div class="panel">
        <div class="panel-header">
            <h2>Liste des scrutins</h2>
            <a href="scrutin_form.php" class="btn-small">+ Nouveau scrutin</a>
        </div>

        <?php if (count($scrutins) === 0): ?>
            <p>Aucun scrutin pour le moment.</p>
        <?php else: ?>
            <table class="table-scrutins">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Compétition</th>
                        <th>Ouverture</th>
                        <th>Clôture</th>
                        <th>État</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($scrutins as $s): ?>
                    <tr>
                        <td><?= htmlspecialchars($s['nom_scrutin']) ?></td>
                        <td><?= htmlspecialchars($s['nom_compet']) ?></td>
                        <td><?= htmlspecialchars($s['date_ouverture']) ?></td>
                        <td><?= htmlspecialchars($s['date_cloture']) ?></td>
                        <td><?= htmlspecialchars($s['etat_scrutin']) ?></td>
                        <td>
                            <a href="scrutin_form.php?id=<?= $s['idscrutin'] ?>">Éditer</a> ·
                            <a href="scrutin_action.php?id=<?= $s['idscrutin'] ?>&action=ouvrir">Ouvrir</a> ·
                            <a href="scrutin_action.php?id=<?= $s['idscrutin'] ?>&action=cloturer">Clôturer</a> ·
                            <a href="scrutin_action.php?id=<?= $s['idscrutin'] ?>&action=supprimer"
                               onclick="return confirm('Supprimer ce scrutin ?');">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</main>
</body>
</html>
