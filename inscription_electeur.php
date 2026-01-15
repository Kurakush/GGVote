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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';
    $role     = trim($_POST['role'] ?? '');

    $roles_valides = ['Public', 'Staff', 'Joueur'];

    // 1) Vérifications
    if ($email === '' || $password === '' || $confirm === '' || $role === '') {
        $message_error = "Tous les champs doivent être remplis.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message_error = "L'email n'est pas valide.";
    } elseif ($password !== $confirm) {
        $message_error = "Les mots de passe ne correspondent pas.";
    } elseif (strlen($password) < 8) {
        $message_error = "Le mot de passe doit contenir au moins 8 caractères.";
    } elseif (!in_array($role, $roles_valides)) {
        $message_error = "Veuillez sélectionner un rôle valide.";
    } else {

        // Vérifier si l'email existe déjà
        $sqlCheck = "SELECT idelecteur FROM electeur WHERE email = :email LIMIT 1";
        $stmtCheck = $connexion->prepare($sqlCheck);
        $stmtCheck->execute([':email' => $email]);

        if ($stmtCheck->fetch()) {
            $message_error = "Un compte existe déjà avec cet email.";
        } else {

            // Insertion
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $sqlInsert = "INSERT INTO electeur (email, mot_de_passe, type, actif, idadmin)
                          VALUES (:email, :mdp, :type, 0, NULL)";
            $stmtInsert = $connexion->prepare($sqlInsert);
            $stmtInsert->execute([
                ':email' => $email,
                ':mdp'   => $hash,
                ':type'  => $role
            ]);

            $message_success = "Votre compte a été créé ! Il doit être validé par un administrateur.";
        }
    }
}
?>

<!-- PAGE INSCRIPTION -->
<div class="auth-page">
    <div class="auth-card">

        <h1 class="auth-title">Créer un compte électeur</h1>

        <?php if ($message_error): ?>
            <div class="auth-alert auth-alert-error"><?= htmlspecialchars($message_error) ?></div>
        <?php endif; ?>

        <?php if ($message_success): ?>
            <div class="auth-alert auth-alert-success"><?= htmlspecialchars($message_success) ?></div>
        <?php endif; ?>

        <form method="post" class="auth-form">

            <div class="auth-field">
                <label class="auth-label">Email</label>
                <input type="email" name="email" class="auth-input"
                       required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>

            <div class="auth-field">
                <label class="auth-label">Mot de passe</label>
                <input type="password" name="password" class="auth-input" required>
            </div>

            <div class="auth-field">
                <label class="auth-label">Confirmation du mot de passe</label>
                <input type="password" name="confirm" class="auth-input" required>
            </div>

            <div class="auth-field">
                <label class="auth-label">Rôle</label>
                <select name="role" class="auth-input" required>
                    <option value="">-- Choisissez un rôle --</option>
                    <option value="Public" <?= (($_POST['role'] ?? '') === 'Public') ? 'selected' : '' ?>>Public</option>
                    <option value="Staff"  <?= (($_POST['role'] ?? '') === 'Staff') ? 'selected' : '' ?>>Staff</option>
                    <option value="Joueur" <?= (($_POST['role'] ?? '') === 'Joueur') ? 'selected' : '' ?>>Joueur</option>
                </select>
            </div>

            

            <button type="submit" class="auth-submit">Créer mon compte</button>

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
