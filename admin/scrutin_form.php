<?php
require "check_admin.php";
require "../dbconnect.php";

$connexion = dbconnect();
if (!$connexion) {
    die("Pb d'accès à la bdd");
}

$mode = "ajout";
$nom_scrutin    = "";
$date_ouverture = "";
$date_cloture   = "";
$idcompetition  = "";

if (isset($_GET['id'])) {
    $mode = "edition";
    $id = (int) $_GET['id'];

    $stmt = $connexion->prepare("SELECT * FROM scrutin WHERE idscrutin = ?");
    $stmt->execute([$id]);
    $scrutin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($scrutin) {
        $nom_scrutin    = $scrutin['nom_scrutin'];
        $date_ouverture = str_replace(' ', 'T', substr($scrutin['date_ouverture'], 0, 16));
        $date_cloture   = str_replace(' ', 'T', substr($scrutin['date_cloture'], 0, 16));
        $idcompetition  = $scrutin['idcompetition'];
    }
}

$stmt = $connexion->query("SELECT idcompetition, nom_compet FROM competition");
$competitions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= ($mode === "ajout" ? "Créer un scrutin" : "Modifier un scrutin") ?> - GGVote</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<header class="admin-header">
    <h1><?= ($mode === "ajout" ? "Créer un scrutin" : "Modifier un scrutin") ?></h1>
    <nav>
        <a href="index.php">Dashboard</a>
        <a href="scrutins.php">Scrutins</a>
        <a href="../index.php?disconnect=1" class="btn-logout">Déconnexion</a>
    </nav>
</header>

<main class="admin-main form-page">
    <section class="panel panel-form">
        <form method="post" action="scrutin_save.php" class="form-admin">
            <input type="hidden" name="mode" value="<?= $mode ?>">
            <?php if ($mode === "edition"): ?>
                <input type="hidden" name="idscrutin" value="<?= (int)$scrutin['idscrutin'] ?>">
            <?php endif; ?>

            <label>Nom du scrutin</label>
            <input type="text" name="nom_scrutin" required
                   value="<?= htmlspecialchars($nom_scrutin) ?>">

            <label>Compétition</label>
            <select name="idcompetition" required>
                <option value="">-- choisir une compétition --</option>
                <?php foreach ($competitions as $c): ?>
                    <option value="<?= $c['idcompetition'] ?>"
                        <?= ($c['idcompetition'] == $idcompetition) ? "selected" : "" ?>>
                        <?= htmlspecialchars($c['nom_compet']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <div class="form-row-2">
                <div>
                    <label>Date d’ouverture</label>
                    <input type="datetime-local" name="date_ouverture" required
                           value="<?= $date_ouverture ?>">
                </div>
                <div>
                    <label>Date de clôture</label>
                    <input type="datetime-local" name="date_cloture" required
                           value="<?= $date_cloture ?>">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Enregistrer</button>
                <a href="scrutins.php" class="btn-secondary">Annuler</a>
            </div>
        </form>
    </section>
</main>

</body>
</html>
