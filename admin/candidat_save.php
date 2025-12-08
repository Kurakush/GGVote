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

    $uploadDir = "../images/"; // dossier où tu ranges les images
    $fileName  = $_FILES['photo']['name'];
    $fileTmp   = $_FILES['photo']['tmp_name'];

    // Sécuriser un peu le nom
    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed   = ['jpg', 'jpeg', 'png', 'webp'];

    
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

    $idcandidat = (int) ($_POST['idcandidat'] ?? 0);

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
