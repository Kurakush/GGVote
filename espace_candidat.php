<?php
require('header.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier la connexion candidat
if (!isset($_SESSION['idcandidat'])) {
    header("Location: index.php");
    exit;
}

$idcandidat = (int)$_SESSION['idcandidat'];

$connexion = dbconnect();
if (!$connexion) {
    die("Erreur d'accès à la base de données.");
}

/* ==================================================
   RÉCUPÉRATION DES INFORMATIONS DU CANDIDAT
   ================================================== */
$sql = "SELECT *
        FROM candidat_user
        WHERE idcandidat = :id";
$stmt = $connexion->prepare($sql);
$stmt->execute([':id' => $idcandidat]);
$candidat = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$candidat) {
    die("Candidat introuvable.");
}

$complete = (int)$candidat['candidature_complete'];
$validee  = (int)$candidat['candidature_validee'];

$message_success = "";
$message_error   = "";

/* ==================================================
   TRAITEMENT DU FORMULAIRE DE MISE À JOUR
   ================================================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {

    $pseudo     = trim($_POST['pseudo'] ?? '');
    $equipe     = trim($_POST['equipe'] ?? '');
    $bio        = trim($_POST['bio'] ?? '');
    $lien_media = trim($_POST['lien_media'] ?? '');

    if ($pseudo === '') {
        $message_error = "Le pseudo ne peut pas être vide.";
    } else {
        $sqlUpdate = "UPDATE candidat_user
                      SET pseudo = :pseudo,
                          equipe = :equipe,
                          bio = :bio,
                          lien_media = :lien
                      WHERE idcandidat = :id";

        $stmtUp = $connexion->prepare($sqlUpdate);
        $stmtUp->execute([
            ':pseudo' => $pseudo,
            ':equipe' => $equipe ?: null,
            ':bio'    => $bio ?: null,
            ':lien'   => $lien_media ?: null,
            ':id'     => $idcandidat
        ]);

        $message_success = "Votre profil a été mis à jour.";
    }
}

/* ==================================================
   SOUMISSION DE LA CANDIDATURE
   ================================================== */
if (isset($_POST['submit_candidature'])) {

    // Vérifier que le profil est complet :
    if (empty($candidat['pseudo']) || empty($candidat['bio'])) {
        $message_error = "Votre profil doit contenir au minimum un pseudo et une bio.";
    } else {
        $sqlSubmit = "UPDATE candidat_user
                      SET candidature_complete = 1
                      WHERE idcandidat = :id";
        $stmtSubmit = $connexion->prepare($sqlSubmit);
        $stmtSubmit->execute([':id' => $idcandidat]);

        $complete = 1;
        $message_success = "Votre candidature est maintenant envoyée. Elle est en attente de validation.";
    }
}

?>

<!-- HTML -->

<div class="profil-container">

    <h1 class="profil-title">Espace candidat</h1>

    <?php if ($message_error): ?>
        <div class="error-box"><?= htmlspecialchars($message_error) ?></div>
    <?php endif; ?>

    <?php if ($message_success): ?>
        <div class="success-box"><?= htmlspecialchars($message_success) ?></div>
    <?php endif; ?>

    <!-- Informations compte -->
    <section class="profil-card">
        <h2 class="profil-card-title">Mes informations</h2>
        <p><strong>Email :</strong> <?= htmlspecialchars($candidat['email']) ?></p>
        <p><strong>Jeu :</strong> <?= htmlspecialchars($candidat['idjeu']) ?></p>
        <p><strong>Compétition :</strong> <?= htmlspecialchars($candidat['idcompetition']) ?></p>
    </section>

    <!-- Formulaire modification profil -->
    <section class="profil-card">
        <h2 class="profil-card-title">Mon profil candidat</h2>

        <form method="post">

            <div class="vote-token-field">
                <label>Pseudo</label>
                <input type="text" name="pseudo"
                       value="<?= htmlspecialchars($candidat['pseudo']) ?>" required>
            </div>

            <div class="vote-token-field">
                <label>Équipe</label>
                <input type="text" name="equipe"
                       value="<?= htmlspecialchars($candidat['equipe']) ?>">
            </div>

            <div class="vote-token-field">
                <label>Bio / présentation</label>
                <textarea name="bio" rows="4"><?= htmlspecialchars($candidat['bio']) ?></textarea>
            </div>

            <div class="vote-token-field">
                <label>Lien média / highlight</label>
                <input type="text" name="lien_media"
                       value="<?= htmlspecialchars($candidat['lien_media']) ?>">
            </div>

            <button type="submit" name="update_profile" class="vote-btn">Mettre à jour</button>
        </form>
    </section>

    <!-- État de la candidature -->
    <section class="profil-card">
        <h2 class="profil-card-title">État de ma candidature</h2>

        <?php if ($validee == 1): ?>
            <p class="success-box">Votre candidature a été validée ✔  
            Vous apparaissez maintenant dans les scrutins.</p>

        <?php elseif ($complete == 1): ?>
            <p class="profil-vote-summary">
                Votre candidature est <strong>complète</strong> et <strong>en attente de validation</strong> par un administrateur.
            </p>

        <?php else: ?>
            <p>Votre candidature n’a pas encore été envoyée.</p>
            <form method="post">
                <button type="submit" name="submit_candidature"
                        class="vote-btn">Soumettre ma candidature</button>
            </form>
        <?php endif; ?>
    </section>

</div>

<?php require('footer.php'); ?>
