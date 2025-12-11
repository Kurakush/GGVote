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

    // 1) Vérifs de base
    if ($email === '' || $password === '' || $confirm === '') {
        $message_error = "Tous les champs sont obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message_error = "L'email n'est pas valide.";
    } elseif ($password !== $confirm) {
        $message_error = "Les mots de passe ne correspondent pas.";
    } elseif (strlen($password) < 8) {
        $message_error = "Le mot de passe doit contenir au moins 8 caractères.";
    } else {

        // 2) Vérifier si l'email existe déjà
        $sqlCheck = "SELECT idelecteur FROM electeur WHERE email = :email LIMIT 1";
        $stmtCheck = $connexion->prepare($sqlCheck);
        $stmtCheck->execute([':email' => $email]);

        if ($stmtCheck->fetch()) {
            $message_error = "Un compte avec cet email existe déjà.";
        } else {
            // 3) Insertion
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $sqlInsert = "INSERT INTO electeur (email, mot_de_passe, type, actif, idadmin)
                          VALUES (:email, :mdp, 'ELECTEUR', 0, NULL)";
            $stmtInsert = $connexion->prepare($sqlInsert);
            $stmtInsert->execute([
                ':email' => $email,
                ':mdp'   => $hash
            ]);

            $message_success = "Votre compte a été créé. 
            Il sera activé par un administrateur avant que vous puissiez voter.";
        }
    }
}
?>

<div class="profil-container">
    <h1 class="profil-title">Inscription électeur</h1>

    <?php if ($message_error): ?>
        <div class="error-box"><?= htmlspecialchars($message_error) ?></div>
    <?php endif; ?>

    <?php if ($message_success): ?>
        <div class="success-box"><?= htmlspecialchars($message_success) ?></div>
    <?php endif; ?>

    <section class="profil-card">
        <h2 class="profil-card-title">Créer un compte électeur</h2>

        <form method="post" class="vote-form">

            <div class="vote-token-field">
                <label for="email">Email</label>
                <input type="email"
                       id="email"
                       name="email"
                       required
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>

            <div class="vote-token-field">
                <label for="password">Mot de passe</label>
                <input type="password"
                       id="password"
                       name="password"
                       required>
            </div>

            <div class="vote-token-field">
                <label for="confirm">Confirmation du mot de passe</label>
                <input type="password"
                       id="confirm"
                       name="confirm"
                       required>
            </div>

            <button type="submit" class="vote-btn">Créer mon compte</button>
        </form>
    </section>
</div>

<?php require 'footer.php'; ?>
