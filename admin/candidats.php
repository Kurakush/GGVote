<?php
require "check_admin.php";
require "../dbconnect.php";

$connexion = dbconnect();
if (!$connexion) {
    die("Pb d'accès à la bdd");
}

// Joueurs + nom de la compétition
$sql = "SELECT j.*, c.nom_compet
        FROM joueur j
        JOIN competition c ON c.idcompetition = j.idcompetition
        ORDER BY c.nom_compet, j.pseudo";
$stmt = $connexion->query($sql);
$joueurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des candidats - GGVote</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<header class="admin-header">
    <h1>Gestion des candidats</h1>
    <nav>
        <a href="index.php">Dashboard</a>
        <a href="scrutins.php">Scrutins</a>
        <a href="electeurs.php">Électeurs</a>
        <a href="candidats.php">Candidats</a>
        <a href="../index.php?disconnect=1" class="btn-logout">Déconnexion</a>
    </nav>
</header>

<main class="admin-main">
    <section class="panel">
        <div class="panel-header">
            <h2>Liste des candidats</h2>
            <a href="candidat_form.php" class="btn-small">+ Nouveau candidat</a>
        </div>

        <?php if (count($joueurs) === 0): ?>
            <p>Aucun candidat n’a encore été ajouté.</p>
        <?php else: ?>
            <table class="table-scrutins">
                <thead>
                    <tr>
                        <th>Pseudo</th>
                        <th>Équipe</th>
                        <th>Poste</th>
                        <th>Âge</th>
                        <th>Nationalité</th>
                        <th>Compétition</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($joueurs as $j): ?>
                    <tr>
                        <td><?= htmlspecialchars($j['pseudo']) ?></td>
                        <td><?= htmlspecialchars($j['equipe']) ?></td>
                        <td><?= htmlspecialchars($j['poste'] ?? '') ?></td>
                        <td><?= (int)$j['age'] ?></td>
                        <td><?= htmlspecialchars($j['nationalite']) ?></td>
                        <td><?= htmlspecialchars($j['nom_compet']) ?></td>
                        <td>
                            <a href="candidat_form.php?id=<?= $j['idjoueur'] ?>">Éditer</a> ·
                            <a href="candidat_action.php?id=<?= $j['idjoueur'] ?>&action=supprimer"
                               onclick="return confirm('Supprimer ce candidat ?');">
                                Supprimer
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
</main>

</body>
</html>
