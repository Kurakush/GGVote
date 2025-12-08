<?php
require "check_admin.php";
require "../dbconnect.php";

$connexion = dbconnect();
if (!$connexion) {
    die("Pb d'accès à la bdd");
}

$mode          = "ajout";
$idjoueur      = null;
$pseudo        = "";
$equipe        = "";
$age           = "";
$nationalite   = "";
$poste         = "";
$idcompetition = "";
$photo = "";

// Si modification
if (isset($_GET['id'])) {
    $mode     = "edition";
    $idjoueur = (int) $_GET['id'];

    $stmt = $connexion->prepare("SELECT * FROM joueur WHERE idjoueur = ?");
    $stmt->execute([$idjoueur]);
    $joueur = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($joueur) {
        $pseudo        = $joueur['pseudo'];
        $equipe        = $joueur['equipe'];
        $age           = $joueur['age'];
        $nationalite   = $joueur['nationalite'];
        $poste         = $joueur['poste'];
        $idcompetition = $joueur['idcompetition'];
        $photo         = $candidat['photo'];
    }
}

// liste des compétitions
$stmt = $connexion->query("SELECT idcompetition, nom_compet FROM competition");
$competitions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= ($mode === "ajout" ? "Ajouter un candidat" : "Modifier un candidat") ?> - GGVote</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<header class="admin-header">
    <h1><?= ($mode === "ajout" ? "Ajouter un candidat" : "Modifier un candidat") ?></h1>
    <nav>
        <a href="index.php">Dashboard</a>
        <a href="candidats.php">Candidats</a>
        <a href="../index.php?disconnect=1" class="btn-logout">Déconnexion</a>
    </nav>
</header>

<main class="admin-main form-page">
    <section class="panel panel-form">
        <form method="post" action="candidat_save.php" class="form-admin" enctype="multipart/form-data">
            <input type="hidden" name="mode" value="<?= $mode ?>">
            <?php if ($mode === "edition"): ?>
                <input type="hidden" name="idjoueur" value="<?= (int)$idjoueur ?>">
            <?php endif; ?>

            <label>Pseudo</label>
            <input type="text" name="pseudo" required value="<?= htmlspecialchars($pseudo) ?>">

            <label>Équipe</label>
            <input type="text" name="equipe" value="<?= htmlspecialchars($equipe) ?>">

            <div class="form-row-2">
                <div>
                    <label>Âge</label>
                    <input type="number" name="age" min="10" max="100"
                           value="<?= htmlspecialchars($age) ?>">
                </div>
                <div>
                    <label>Poste</label>
                    <input type="text" name="poste" value="<?= htmlspecialchars($poste) ?>">
                </div>
            </div>

            <label>Nationalité</label>
            <input type="text" name="nationalite" value="<?= htmlspecialchars($nationalite) ?>">

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
            
            <label>Photo du joueur</label>
            <input type="file" name="photo" accept="image/*">

            <?php if ($mode === "edition" && !empty($photo)): ?>
                <p>Photo actuelle :</p>
                <img src="../images/<?= htmlspecialchars($photo) ?>" alt="Photo actuelle"
                     style="max-width: 200px; border-radius: 12px; display:block; margin-bottom:10px;">
            <?php endif; ?>

            <!-- pour garder l'ancienne photo si on n'en choisit pas une nouvelle -->
            <input type="hidden" name="old_photo" value="<?= htmlspecialchars($photo) ?>">

            <div class="form-actions">
                <button type="submit" class="btn-primary">Enregistrer</button>
                <a href="candidats.php" class="btn-secondary">Annuler</a>
            </div>
        </form>
    </section>
</main>

</body>
</html>
