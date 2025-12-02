<?php
// /admin/tokens_generate.php
require_once __DIR__ . '/../dbconnect.php';

// 1) Fonctions de génération
function generateToken($length = 32): string {
    return bin2hex(random_bytes($length / 2));
}

function createTokens(PDO $pdo, int $adminId, int $nbTokens): array {
    $tokensCrees = [];

    for ($i = 0; $i < $nbTokens; $i++) {
        while (true) {
            $token = generateToken(32);

            $stmt = $pdo->prepare("
                INSERT INTO token (idtoken, etat, date_generation, idadmin, idelecteur)
                VALUES (:idtoken, 0, NOW(), :idadmin, NULL)
            ");

            try {
                $stmt->execute([
                    ':idtoken' => $token,
                    ':idadmin' => $adminId,
                ]);
                $tokensCrees[] = $token;
                break; // on passe au token suivant
            } catch (PDOException $e) {
                if ($e->getCode() === '23000') {
                    // doublon -> on régénère
                    continue;
                } else {
                    throw $e;
                }
            }
        }
    }

    return $tokensCrees;
}

// 2) Traitement du formulaire
$tokensGeneres = [];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nbTokens = (int)($_POST['nb_tokens'] ?? 0);
    $adminId  = 1; // TODO : remplacer par l'ID de l'admin connecté (session)

    if ($nbTokens > 0) {
        $tokensGeneres = createTokens($pdo, $adminId, $nbTokens);
        $message = $nbTokens . " token(s) généré(s).";
    } else {
        $message = "Veuillez indiquer un nombre de tokens > 0.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Génération de tokens</title>
</head>
<body>
    <h1>Générer des tokens de vote</h1>

    <form method="post">
        <label>Nombre de tokens à générer :
            <input type="number" name="nb_tokens" min="1" required>
        </label>
        <button type="submit">Générer</button>
    </form>

    <?php if ($message): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <?php if (!empty($tokensGeneres)): ?>
        <h2>Tokens générés</h2>
        <ul>
            <?php foreach ($tokensGeneres as $t): ?>
                <li><?= htmlspecialchars($t) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>
</html>
