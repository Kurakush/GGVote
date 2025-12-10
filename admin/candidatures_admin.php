<?php
require "check_admin.php";
require "../dbconnect.php";

$connexion = dbconnect();
if (!$connexion) {
    die("Pb d'accès à la bdd");
}

// Candidatures en attente : complète = 1, validée = 0
$sql = "SELECT j.idjoueur, j.pseudo, j.email_candidat, j.equipe,
               j.bio_candidat, j.candidature_complete, j.candidature_validee,
               c.nom_compet
        FROM joueur j
        LEFT JOIN competition c ON j.idcompetition = c.idcompetition
        WHERE j.candidature_complete = 1
          AND j.candidature_validee = 0
        ORDER BY c.nom_compet, j.pseudo";

$stmt = $connexion->query($sql);
$candidats = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Validation des candidatures</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<header class="admin-header">
    <h1>Candidatures en attente</h1>
    <nav>
        <a href="index.php">Dashboard</a>
        <a href="candidats.php">Liste candidats</a>
        <a href="candidatures_admin.php">Candidatures</a>
        <a href="../index.php?disconnect=1" class="btn-logout">Déconnexion</a>
    </nav>
</header>

<main class="admin-main">

<?php if (isset($_SESSION['flash_message'])): ?>
    <p class="flash-success"><?= $_SESSION['flash_message']; ?></p>
    <?php unset($_SESSION['flash_message']); ?>
<?php endif; ?>

<?php if (empty($candidats)): ?>
    <p>Aucune candidature en attente.</p>
<?php else: ?>
    <table class="table-scrutins">
        <thead>
            <tr>
                <th>Pseudo</th>
                <th>Email</th>
                <th>Équipe</th>
                <th>Bio</th>
                <th>Compétition</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
        <?php foreach ($candidats as $cand): ?>
            <tr>
                <td><?= htmlspecialchars($cand['pseudo']) ?></td>
                <td><?= htmlspecialchars($cand['email_candidat']) ?></td>
                <td><?= htmlspecialchars($cand['equipe']) ?></td>
                <td><?= nl2br(htmlspecialchars($cand['bio_candidat'])) ?></td>
                <td><?= htmlspecialchars($cand['nom_compet']) ?></td>

                <td>
                    <form action="candidatures_save.php" method="post" style="display:inline;">
                        <input type="hidden" name="idjoueur" value="<?= (int)$cand['idjoueur'] ?>">

                        <button type="submit" name="action" value="valider">Valider</button>

                        <button type="submit"
                                name="action" value="refuser"
                                onclick="return confirm('Refuser cette candidature ?');">
                            Refuser
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</main>

</body>
</html>
