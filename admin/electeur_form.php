<?php
require "check_admin.php";
require "../dbconnect.php";

$connexion = dbconnect();
if (!$connexion) {
    die("Pb d'accès à la bdd");
}

$mode          = "ajout";
$idelecteur    = null;
$email         = "";
$type          = "ELECTEUR";

if (isset($_GET['id'])) {
    $mode = "edition";
    $idelecteur = (int) $_GET['id'];

    $stmt = $connexion->prepare("SELECT * FROM electeur WHERE idelecteur = ?");
    $stmt->execute([$idelecteur]);
    $electeur = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($electeur) {
        $email = $electeur['email'];
        $type  = $electeur['type'];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= ($mode === "ajout" ? "Ajouter un électeur" : "Modifier un électeur") ?> - GGVote</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<header class="admin-header">
    <h1><?= ($mode === "ajout" ? "Ajouter un électeur" : "Modifier un électeur") ?></h1>
    <nav>
        <a href="index.php">Dashboard</a>
        <a href="electeurs.php">Électeurs</a>
        <a href="../index.php?disconnect=1" class="btn-logout">Déconnexion</a>
    </nav>
</header>

<main class="admin-main form-page">
    <section class="panel panel-form">
        <form method="post" action="electeur_save.php" class="form-admin">
            <input type="hidden" name="mode" value="<?= $mode ?>">
            <?php if ($mode === "edition"): ?>
                <input type="hidden" name="idelecteur" value="<?= (int)$idelecteur ?>">
            <?php endif; ?>

            <label>Email</label>
            <input type="email" name="email" required value="<?= htmlspecialchars($email) ?>">

            <label>Mot de passe <?= ($mode === "edition" ? "(laisser vide pour ne pas changer)" : "") ?></label>
            <input type="password" name="password">

            <label>Type</label>
            <select name="type">
                <option value="Staff"   <?= ($type === "Staff"   ? "selected" : "") ?>>Staff</option>
                <option value="Joueur"  <?= ($type === "Joueur"  ? "selected" : "") ?>>Joueur</option>
                <option value="Spectateur"  <?= ($type === "Public"  ? "selected" : "") ?>>Public</option>
            </select>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Enregistrer</button>
                <a href="electeurs.php" class="btn-secondary">Annuler</a>
            </div>
        </form>
    </section>
</main>

</body>
</html>
