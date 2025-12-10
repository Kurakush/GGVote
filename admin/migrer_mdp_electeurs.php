<?php
// Script de migration des mots de passe des électeurs
// À lancer UNE FOIS, en étant connecté en admin.

require "check_admin.php";    // sécurise l'accès
require "../dbconnect.php";

$connexion = dbconnect();
if (!$connexion) {
    die("Pb d'accès à la bdd");
}

// Récupérer tous les électeurs
$sql = "SELECT idelecteur, email, mot_de_passe 
        FROM electeur";
$stmt = $connexion->prepare($sql);
$stmt->execute();

$updated = 0;
$skipped = 0;

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $id    = (int)$row['idelecteur'];
    $email = $row['email'];
    $pwd   = $row['mot_de_passe'];

    // Si pas de mot de passe ou null → on ignore
    if ($pwd === null || $pwd === '') {
        $skipped++;
        continue;
    }

    // Si déjà hashé (bcrypt / argon2 / etc.) → on ignore
    if (
        str_starts_with($pwd, '$2y$') ||
        str_starts_with($pwd, '$2a$') ||
        str_starts_with($pwd, '$2b$') ||
        str_starts_with($pwd, '$argon2')
    ) {
        $skipped++;
        continue;
    }

    // Ici : on considère que c'est un ancien mot de passe en clair → on le hash
    $hash = password_hash($pwd, PASSWORD_DEFAULT);

    $sqlUpdate = "UPDATE electeur
                  SET mot_de_passe = :hash
                  WHERE idelecteur = :id";
    $stmtUpdate = $connexion->prepare($sqlUpdate);
    $stmtUpdate->execute([
        ':hash' => $hash,
        ':id'   => $id
    ]);

    $updated++;
}

// Affichage d'un petit résumé
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Migration mots de passe électeurs</title>
</head>
<body style="background:#202020;color:#D9D9D9;font-family:'Exo 2',sans-serif;">
    <h1>Migration des mots de passe des électeurs</h1>
    <p>Mots de passe mis à jour (hashés) : <strong><?= $updated ?></strong></p>
    <p>Électeurs ignorés (déjà hashés ou mdp vide) : <strong><?= $skipped ?></strong></p>
    <p><a href="electeurs.php" style="color:#e31919;">Retour à la gestion des électeurs</a></p>
</body>
</html>
