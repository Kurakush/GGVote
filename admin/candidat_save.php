<?php
require "check_admin.php";
require "../dbconnect.php";

$connexion = dbconnect();
if (!$connexion) {
    die("Pb d'accès à la bdd");
}

$mode          = $_POST['mode'] ?? 'ajout';
$pseudo        = $_POST['pseudo'] ?? '';
$equipe        = $_POST['equipe'] ?? '';
$age           = $_POST['age'] ?? null;
$nationalite   = $_POST['nationalite'] ?? '';
$poste         = $_POST['poste'] ?? '';
$idcompetition = (int) ($_POST['idcompetition'] ?? 0);
$idadmin       = $_SESSION['admin_id'];   // admin connecté
$old_photo = $_POST['old_photo'] ?? '';

// Gestion de la photo
$photo = $old_photo; // par défaut, on garde l'ancienne

if (!empty($_FILES['photo']['name'])) {

    $uploadDir = "../images/";
    $fileName  = $_FILES['photo']['name'];
    $fileTmp   = $_FILES['photo']['tmp_name'];

    // On récupère juste le nom + extension, on enlève le chemin éventuel
    $baseName = basename($fileName);

    // Petit nettoyage : espaces → tirets, tout en minuscules
    $baseName = str_replace(' ', '-', $baseName);
    $baseName = strtolower($baseName);

    $extension = strtolower(pathinfo($baseName, PATHINFO_EXTENSION));
    $allowed   = ['jpg', 'jpeg', 'png', 'webp'];

    if (in_array($extension, $allowed)) {
        // Pas de uniqid ici : on garde le nom choisi (nettoyé)
        if (move_uploaded_file($fileTmp, $uploadDir . $baseName)) {
            $photo = $baseName;
        }
    }
}

if ($mode === "ajout") {

    $sql = "INSERT INTO joueur (pseudo, equipe, age, nationalite, poste, idadmin, idcompetition, photo)
            VALUES (:pseudo, :equipe, :age, :nationalite, :poste, :idadmin, :idcomp, :photo)";
    $stmt = $connexion->prepare($sql);
    $stmt->execute([
        ':pseudo'      => $pseudo,
        ':equipe'      => $equipe,
        ':age'         => ($age !== '' ? $age : null),
        ':nationalite' => $nationalite,
        ':poste'       => $poste,
        ':idadmin'     => $idadmin,
        ':idcomp'      => $idcompetition,
        ':photo'       => $photo
    ]);

} else { // edition

    $idcandidat = (int) ($_POST['idjoueur'] ?? 0);

    $sql = "UPDATE joueur
            SET pseudo = :pseudo,
                equipe = :equipe,
                age = :age,
                nationalite = :nationalite,
                poste = :poste,
                idcompetition = :idcomp,
                photo = :photo
            WHERE idjoueur = :id";
    $stmt = $connexion->prepare($sql);
    $stmt->execute([
        ':pseudo'      => $pseudo,
        ':equipe'      => $equipe,
        ':age'         => ($age !== '' ? $age : null),
        ':nationalite' => $nationalite,
        ':poste'       => $poste,
        ':idcomp'      => $idcompetition,
        ':photo'       => $photo,
        ':id'          => $idcandidat
    ]);

}

header("Location: candidats.php");
exit;
