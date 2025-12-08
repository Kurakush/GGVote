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

if ($mode === "ajout") {

    $sql = "INSERT INTO joueur (pseudo, equipe, age, nationalite, poste, idadmin, idcompetition)
            VALUES (:pseudo, :equipe, :age, :nationalite, :poste, :idadmin, :idcomp)";
    $stmt = $connexion->prepare($sql);
    $stmt->execute([
        ':pseudo'      => $pseudo,
        ':equipe'      => $equipe,
        ':age'         => $age !== '' ? $age : null,
        ':nationalite' => $nationalite,
        ':poste'       => $poste,
        ':idadmin'     => $idadmin,
        ':idcomp'      => $idcompetition
    ]);

} else { // edition

    $idjoueur = (int) ($_POST['idjoueur'] ?? 0);

    $sql = "UPDATE joueur
            SET pseudo = :pseudo,
                equipe = :equipe,
                age = :age,
                nationalite = :nationalite,
                poste = :poste,
                idcompetition = :idcomp
            WHERE idjoueur = :id";
    $stmt = $connexion->prepare($sql);
    $stmt->execute([
        ':pseudo'      => $pseudo,
        ':equipe'      => $equipe,
        ':age'         => $age !== '' ? $age : null,
        ':nationalite' => $nationalite,
        ':poste'       => $poste,
        ':idcomp'      => $idcompetition,
        ':id'          => $idjoueur
    ]);
}

header("Location: candidats.php");
exit;
