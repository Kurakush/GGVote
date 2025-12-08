<?php
require "check_admin.php";
require "../dbconnect.php";

$connexion = dbconnect();
if (!$connexion) {
    die("Pb d'accès à la bdd");
}

$mode           = $_POST['mode'] ?? 'ajout';
$nom_scrutin    = $_POST['nom_scrutin'] ?? '';
$idcompetition  = (int) ($_POST['idcompetition'] ?? 0);
$date_ouverture = $_POST['date_ouverture'] ?? '';
$date_cloture   = $_POST['date_cloture'] ?? '';

// datetime-local arrive sous forme "YYYY-MM-DDTHH:MM" → on remet un espace
$date_ouverture = str_replace('T', ' ', $date_ouverture) . ':00';
$date_cloture   = str_replace('T', ' ', $date_cloture) . ':00';

$idadmin = $_SESSION['admin_id']; // récupéré à la connexion

if ($mode === "ajout") {
    $sql = "INSERT INTO scrutin (nom_scrutin, date_ouverture, date_cloture, etat_scrutin, idadmin, idcompetition)
            VALUES (:nom, :ouv, :clo, 'en_attente', :idadmin, :idcomp)";
    $stmt = $connexion->prepare($sql);
    $stmt->execute([
        ':nom'     => $nom_scrutin,
        ':ouv'     => $date_ouverture,
        ':clo'     => $date_cloture,
        ':idadmin' => $idadmin,
        ':idcomp'  => $idcompetition
    ]);

} else { // edition
    $idscrutin = (int) ($_POST['idscrutin'] ?? 0);

    $sql = "UPDATE scrutin
            SET nom_scrutin = :nom,
                date_ouverture = :ouv,
                date_cloture = :clo,
                idcompetition = :idcomp
            WHERE idscrutin = :id";
    $stmt = $connexion->prepare($sql);
    $stmt->execute([
        ':nom'    => $nom_scrutin,
        ':ouv'    => $date_ouverture,
        ':clo'    => $date_cloture,
        ':idcomp' => $idcompetition,
        ':id'     => $idscrutin
    ]);
}

header("Location: scrutins.php");
exit;
