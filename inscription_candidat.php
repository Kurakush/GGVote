<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'header.php';

$connexion = dbconnect();
if (!$connexion) {
    die("Pb d'accès à la base de données.");
}

$message_success = "";
$message_error   = "";

/* ===============================
   RÉCUPÉRATION DES COMPÉTITIONS
   =============================== */
$sqlC = "SELECT idcompetition, nom_compet
         FROM competition
         ORDER BY nom_compet";
$stmtC = $connexion->query($sqlC);
$competitions = $stmtC->fetchAll(PDO::FETCH_ASSOC);

/* ===============================
   TRAITEMENT DU FORMULAIRE
   =============================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $pseudo        = trim($_POST['pseudo'] ?? '');
    $email         = trim($_POST['email'] ?? '');
    $password      = $_POST['password'] ?? '';
    $confirm       = $_POST['confirm'] ?? '';
    $idcompetition = (int)($_POST['idcompetition'] ?? 0);

    // Checkbox CGU / Mentions légales
    $accept_cgu = isset($_POST['accept_cgu']);

    // Vérifications
    if (
        $pseudo === '' ||
        $email === '' ||
        $password === '' ||
        $confirm === '' ||
        $idcompetition <= 0
    ) {
        $message_error = "Tous les champs doivent être remplis.";
    } elseif (!$accept_cgu) {
        $message_error = "Vous devez accepter les conditions d'utilisation et les mentions légales.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message_error = "L'email n'est pas valide.";
    } elseif ($password !== $confirm) {
        $message_error = "Les mots de passe ne correspondent pas.";
    } elseif (strlen($password) < 8) {
        $message_error = "Le mot de passe doit contenir au moins 8 caractères.";
    } else {

        // Vérifier que la compétition existe
        $sqlCompOk = "SELECT idcompetition
                      FROM competition
                      WHERE idcompetition = :id
                      LIMIT 1";
        $stmtCompOk = $connexion->prepare($sqlCompOk);
        $stmtCompOk->execute([':id' => $idcompetition]);

        if (!$stmtCompOk->fetch()) {
            $message_error = "Compétition invalide.";
        } else {

            // Vérifier email candidat déjà utilisé
            $sqlCheck = "SELECT idjoueur
                         FROM joueur
                         WHERE email_candidat = :email
                         LIMIT 1";
            $stmtCheck = $connexion->prepare($sqlCheck);
            $stmtCheck->execute([':email' => $email]);

            if ($stmtCheck->fetch()) {
                $message_error = "Un compte candidat existe déjà avec cet email.";
            } else {

                // Insertion candidat
                $hash = password_hash($password, PASSWORD_DEFAULT);

                $sqlInsert = "INSERT INTO joueur
                    (pseudo, email_candidat, mdp_candidat, idcompetition,
                     candidature_complete, candidature_validee, idadmin)
                    VALUES
                    (:pseudo, :email, :mdp, :idcompetition, 0, 0, :idadmin)";

                $idadmin_defaut = 1;
                $stmtInsert = $connexion->prepare($sqlInsert);
                $stmtInsert->execute([
                    ':pseudo'        => $pseudo,
                    ':email'         => $email,
                    ':mdp'           => $hash,
                    ':idcompetition' => $idcompetition,
                    ':idadmin'       => $idadmin_defaut
                ]);

                $message_success =
                    "Compte candidat créé avec succès !
                     Vous pouvez maintenant vous connecter,
                     compléter votre profil et soumettre votre candidature.";
            }
        }
    }
}
?>

<!-- ===============================
     PAGE INSCRIPTION CANDIDAT
     =============================== -->
<div class="auth-page">
    <div class="auth-card">

        <h1 class="auth-title">Créer un compte candidat</h1>

        <?php if ($message_error): ?>
            <div class="auth-alert auth-alert-error">
                <?= htmlspecialchars($message_error) ?>
            </div>
        <?php endif; ?>

        <?php if ($message_success): ?>
            <div class="auth-alert auth-alert-success">
                <?= htmlspecialchars($message_success) ?>
            </div>
        <?php endif; ?>

        <form method="post" class="auth-form">

            <div class="auth-field">
                <label class="auth-label" for="pseudo">Pseudo</label>
                <input type="text"
                    id="pseudo"
                    name="pseudo"
                    class="auth-input"
                    required
                    value="<?= htmlspecialchars($_POST['pseudo'] ?? '') ?>">
            </div>

            <div class="auth-field">
                <label class="auth-label" for="email">Email</label>
                <input type="email"
                    id="email"
                    name="email"
                    class="auth-input"
                    required
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>

            <div class="auth-field">
                <label class="auth-label" for="password">Mot de passe</label>
                <input type="password"
                    id="password"
                    name="password"
                    class="auth-input"
                    required>
            </div>

            <div class="auth-field">
                <label class="auth-label" for="confirm">Confirmation du mot de passe</label>
                <input type="password"
                    id="confirm"
                    name="confirm"
                    class="auth-input"
                    required>
            </div>

            <div class="auth-field">
                <label class="auth-label" for="idcompetition">Compétition</label>
                <select id="idcompetition"
                    name="idcompetition"
                    class="auth-input"
                    required>
                    <option value="">-- Choisissez une compétition --</option>
                    <?php foreach ($competitions as $c): ?>
                        <option value="<?= (int)$c['idcompetition'] ?>"
                            <?= ((int)($_POST['idcompetition'] ?? 0) === (int)$c['idcompetition']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['nom_compet']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="auth-submit">
                Créer mon compte
            </button>

            <div class="auth-field auth-checkbox">
                <label class="auth-checkbox-label">
                    <input type="checkbox"
                        name="accept_cgu"
                        value="1"
                        required
                        <?= isset($_POST['accept_cgu']) ? 'checked' : '' ?>>
                    <span>
                        J'accepte les
                        <a href="mentions_legales.php" target="_blank">mentions légales</a>
                        et les
                        <a href="cgu.php" target="_blank">conditions d'utilisation</a>
                    </span>
                </label>
            </div>

        </form>

    </div>
</div>

<?php require 'footer.php'; ?>