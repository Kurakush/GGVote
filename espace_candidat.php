<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier la connexion candidat
if (!isset($_SESSION['idjoueur_candidat'])) {
    header("Location: index.php");
    exit;
}

require('header.php');

$idjoueur = (int)$_SESSION['idjoueur_candidat'];

$connexion = dbconnect();
if (!$connexion) {
    die("Erreur d'accès à la base de données.");
}

/* ========= RÉCUP INFOS JOUEUR ========= */
$sql = "SELECT *
        FROM joueur
        WHERE idjoueur = :id";
$stmt = $connexion->prepare($sql);
$stmt->execute([':id' => $idjoueur]);
$cand = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cand) {
    die("Candidat introuvable.");
}

$complete = (int)$cand['candidature_complete'];
$validee  = (int)$cand['candidature_validee'];

$message_success = "";
$message_error   = "";

/* ========= MISE À JOUR PROFIL ========= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {

    $pseudo     = trim($_POST['pseudo'] ?? '');
    $equipe     = trim($_POST['equipe'] ?? '');
    $poste = trim($_POST['poste'] ?? '');
    $bio        = trim($_POST['bio'] ?? '');
    $lien_media = trim($_POST['lien_media'] ?? '');
    $age         = (int)($_POST['age'] ?? 0);
    $nationalite = trim($_POST['nationalite'] ?? '');

    $photo_filename = $cand['photo'] ?? null;

if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {

    $tmp  = $_FILES['photo']['tmp_name'];
    $name = $_FILES['photo']['name'];

    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($ext, $allowed, true)) {
        $message_error = "Format de photo invalide (jpg, jpeg, png, webp).";
    } else {

        $photo_filename = basename($name);

        $dest = __DIR__ . '/images/' . $photo_filename;

        if (!move_uploaded_file($tmp, $dest)) {
            $message_error = "Impossible d'enregistrer la photo.";
        }
    }
}


    if ($pseudo === '') {
        $message_error = "Le pseudo ne peut pas être vide.";
    } else {
        $sqlUpdate = "UPDATE joueur
                      SET pseudo = :pseudo,
                          equipe = :equipe,
                          poste = :poste,
                          age = :age,
                          nationalite = :nationalite,
                          bio_candidat = :bio,
                          lien_media = :lien,
                          photo = :photo
                      WHERE idjoueur = :id";

        $stmtUp = $connexion->prepare($sqlUpdate);
        $stmtUp->execute([
            ':pseudo' => $pseudo,
            ':equipe' => $equipe ?: null,
            ':poste'  => $poste ?: null,
            ':age'    => $age > 0 ? $age : null,
            ':nationalite' => ($nationalite !== '' ? $nationalite : null),
            ':bio'    => $bio ?: null,
            ':lien'   => $lien_media ?: null,
            ':photo'  => $photo_filename ?: null,
            ':id'     => $idjoueur
        ]);

        $message_success = "Votre profil a été mis à jour.";
        // mettre à jour les données locales
        $cand['pseudo']       = $pseudo;
        $cand['equipe']       = $equipe;
        $cand['poste'] = ($poste !== '' ? $poste : null);
        $cand['age'] = ($age > 0 ? $age : null);
        $cand['nationalite'] = ($nationalite !== '' ? $nationalite : null);
        $cand['bio_candidat'] = $bio;
        $cand['lien_media']   = $lien_media;
        $cand['photo'] = $photo_filename ?: null;

    }
}

/* ========= SOUMISSION CANDIDATURE ========= */
if (isset($_POST['submit_candidature'])) {

    if (empty($cand['pseudo']) || empty($cand['bio_candidat'])) {
        $message_error = "Votre profil doit au minimum contenir un pseudo et une bio.";
    } else {
        $sqlSubmit = "UPDATE joueur
                      SET candidature_complete = 1
                      WHERE idjoueur = :id";
        $stmtSubmit = $connexion->prepare($sqlSubmit);
        $stmtSubmit->execute([':id' => $idjoueur]);

        $complete = 1;
        $message_success = "Votre candidature est envoyée. Elle est en attente de validation.";
    }
}

?>

<div class="profil-container">

    <h1 class="profil-title">Espace candidat</h1>

    <?php if ($message_error): ?>
        <div class="error-box"><?= htmlspecialchars($message_error) ?></div>
    <?php endif; ?>

    <?php if ($message_success): ?>
        <div class="success-box"><?= htmlspecialchars($message_success) ?></div>
    <?php endif; ?>

    <section class="profil-card">
        <h2 class="profil-card-title">Mon profil</h2>

        <form method="post" enctype="multipart/form-data">

            <div class="vote-token-field">
                <label>Pseudo</label>
                <input type="text" name="pseudo"
                       value="<?= htmlspecialchars($cand['pseudo']) ?>" required>
            </div>

            <div class="vote-token-field">
                <label>Équipe</label>
                <input type="text" name="equipe"
                       value="<?= htmlspecialchars($cand['equipe'] ?? '') ?>">
            </div>

            <div class="vote-token-field">
                <label>Poste</label>
                <input type="text" name="poste"
                       value="<?= htmlspecialchars($cand['poste'] ?? '') ?>">
            </div>

            <div class="vote-token-field">
                <label>Âge</label>
                <input type="number" name="age" min="0" max="120"
                        value="<?= htmlspecialchars($cand['age'] ?? '') ?>">
            </div>

            <div class="vote-token-field">
                <label>Nationalité</label>
                <input type="text" name="nationalite"
                        value="<?= htmlspecialchars($cand['nationalite'] ?? '') ?>">
            </div>

            <div class="vote-token-field">
                <label>Bio / présentation</label>
                <textarea name="bio" rows="4"><?= htmlspecialchars($cand['bio_candidat'] ?? '') ?></textarea>
            </div>

            <div class="vote-token-field">
                <label>Lien média / highlight</label>
                <input type="text" name="lien_media"
                       value="<?= htmlspecialchars($cand['lien_media'] ?? '') ?>">
            </div>

            <div class="vote-token-field">
                <label>Photo (jpg / png / webp)</label>

                <?php if (!empty($cand['photo'])): ?>
                    <div style="margin-bottom:10px;">
                        <img src="images/<?= htmlspecialchars($cand['photo']) ?>"
                            alt="Photo candidat"
                            style="width:120px;height:120px;object-fit:cover;border-radius:12px;border:1px solid #545454;">
                    </div>
                <?php endif; ?>

                <input type="file" name="photo" accept=".jpg,.jpeg,.png,.webp">
            </div>

            <button type="submit" name="update_profile" class="vote-btn">
                Mettre à jour
            </button>
        </form>
    </section>

   <section class="profil-card">
    <h2 class="profil-card-title">État de ma candidature</h2>

    <?php if ($validee == 1): ?>

        <p class="success-box">
            Votre candidature a été validée ✔<br>
            Vous participez officiellement aux scrutins.
        </p>

    <?php elseif ($complete == 1): ?>

        <p>
            Votre candidature est <strong>complète</strong> et
            <strong>en attente de validation</strong> par un administrateur.
        </p>

    <?php else: ?>

        <p>Votre candidature n’a pas encore été envoyée.</p>
        <form method="post">
            <button type="submit" name="submit_candidature" class="vote-btn">
                Soumettre ma candidature
            </button>
        </form>

    <?php endif; ?>
</section>

</div>

<?php require('footer.php'); ?>
