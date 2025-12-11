<?php
require "check_admin.php";
require "../dbconnect.php";

$connexion = dbconnect();
if (!$connexion) {
    die("Pb d'accès à la bdd");
}

$sql = "SELECT * FROM electeur ORDER BY idelecteur ASC";
$stmt = $connexion->query($sql);
$electeurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des électeurs - GGVote</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<header class="admin-header">
    <h1>Gestion des électeurs</h1>
    <nav>
        <a href="index.php">Dashboard</a>
        <a href="scrutins.php">Scrutins</a>
        <a href="electeurs.php">Électeurs</a>
        <a href="candidats.php">Candidats</a>
        <a href="../index.php?disconnect=1" class="btn-logout">Déconnexion</a>
    </nav>
</header>

<main class="admin-main">
    <?php if (isset($_SESSION['flash_message_admin'])): ?>
        <p class="flash-success">
            <?= htmlspecialchars($_SESSION['flash_message_admin']); ?>
        </p>
        <?php unset($_SESSION['flash_message_admin']); ?>
    <?php endif; ?>
    
    <section class="panel">
        <div class="panel-header">
            <h2>Liste des électeurs</h2>
            <a href="electeur_form.php" class="btn-small">+ Nouvel électeur</a>
        </div>

        <?php if (count($electeurs) === 0): ?>
            <p>Aucun électeur pour le moment.</p>
        <?php else: ?>
            <table class="table-scrutins">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Email</th>
                        <th>Type</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($electeurs as $e): ?>
                    <tr>
                        <td><?= (int)$e['idelecteur'] ?></td>
                        <td><?= htmlspecialchars($e['email']) ?></td>
                        <td><?= htmlspecialchars($e['type']) ?></td>
                        <td>
                            <?php
                                $actif  = isset($e['actif']) ? (int)$e['actif'] : 1;
                                $adminCreator = $e['idadmin'] ?? null;

                                if ($actif) {
                                    echo "Actif";
                                } else {
                                    // compte pas encore activé
                                    if ($adminCreator === null) {
                                        echo "En attente de validation";
                                    } else {
                                        echo "Désactivé";
                                     }
                                }
                            ?>
                        </td>
                        <td>
                            <a href="electeur_form.php?id=<?= $e['idelecteur'] ?>">Éditer</a> ·

                            <?php if ($actif): ?>
                                <a href="electeur_action.php?id=<?= $e['idelecteur'] ?>&action=desactiver">
                                    Désactiver
                                </a> ·
                            <?php else: ?>
                                <a href="electeur_action.php?id=<?= $e['idelecteur'] ?>&action=activer">
                                    Activer
                                </a> ·
                            <?php endif; ?>

                            <a href="electeur_action.php?id=<?= $e['idelecteur'] ?>&action=supprimer"
                               onclick="return confirm('Supprimer définitivement cet électeur ?');">
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
