<?php
require "check_admin.php";
require "../dbconnect.php";

$connexion = dbconnect();
if (!$connexion) {
    die("Pb d'accès à la bdd");
}

$mode          = $_POST['mode'] ?? 'ajout';
$pseudo        = $_POST['pseudo'] ?? '';
$email_candidat = trim($_POST['email_candidat'] ?? '');
$mdp_candidat   = $_POST['mdp_candidat'] ?? '';
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

/* =========================
   Hash du mdp (si rempli)
   ========================= */
$mdp_hash = null;
if (!empty($mdp_candidat)) {
    $mdp_hash = password_hash($mdp_candidat, PASSWORD_DEFAULT);
}

if ($mode === "ajout") {

// En ajout : on exige email + mdp
    if (empty($email_candidat) || empty($mdp_candidat)) {
        die("Email et mot de passe candidat obligatoires.");
    }

    $sql = "INSERT INTO joueur (pseudo, email_candidat, mdp_candidat, equipe, age, nationalite, poste, idadmin, idcompetition, photo)
            VALUES (:pseudo, :email_candidat, :mdp_candidat, :equipe, :age, :nationalite, :poste, :idadmin, :idcomp, :photo)";
    $stmt = $connexion->prepare($sql);
    $stmt->execute([
        ':pseudo'      => $pseudo,
        ':email_candidat' => $email_candidat,
        ':mdp_candidat'   => $mdp_hash,
        ':equipe'      => $equipe,
        ':age'         => ($age !== '' ? $age : null),
        ':nationalite' => $nationalite,
        ':poste'       => $poste,
        ':idadmin'     => $idadmin,
        ':idcomp'      => $idcompetition,
        ':photo'       => $photo
    ]);

} else { // edition

// En édition : si mdp vide => on ne modifie pas mdp_candidat
    $idcandidat = (int) ($_POST['idjoueur'] ?? 0);

    $sql = "UPDATE joueur
            SET pseudo = :pseudo,
                email_candidat = :email_candidat,
                mdp_candidat = :mdp_candidat,
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
        ':email_candidat' => $email_candidat,
        ':mdp_candidat' => $mdp_hash,
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
