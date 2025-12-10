<?php
// Petit outil perso pour générer des hash de mot de passe admin
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mdp = $_POST['password'] ?? '';
    $hash = password_hash($mdp, PASSWORD_DEFAULT);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Générateur de hash</title>
</head>
<body style="background:#202020;color:#D9D9D9;font-family:'Exo 2',sans-serif;">
    <h1>Générer un hash de mot de passe</h1>
    <form method="post">
        <label>Mot de passe à hasher :</label>
        <input type="text" name="password" required>
        <button type="submit">Générer</button>
    </form>

    <?php if (!empty($hash)): ?>
        <p>Hash généré :</p>
        <pre><?= htmlspecialchars($hash) ?></pre>
    <?php endif; ?>
</body>
</html>
