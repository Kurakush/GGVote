<?php
require "check_admin.php";
require "../dbconnect.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: candidatures_admin.php");
    exit;
}

$idjoueur = (int)($_POST['idjoueur'] ?? 0);
$action   = $_POST['action'] ?? '';

if ($idjoueur <= 0) {
    header("Location: candidatures_admin.php");
    exit;
}

$connexion = dbconnect();

if ($action === "valider") {

    $sql = "UPDATE joueur
            SET candidature_validee = 1
            WHERE idjoueur = :id";
    $msg = "Candidature validée.";

} elseif ($action === "refuser") {

    $sql = "UPDATE joueur
            SET candidature_complete = 0,
                candidature_validee  = 0
            WHERE idjoueur = :id";
    $msg = "Candidature refusée. Le candidat pourra la modifier et la renvoyer.";

} else {
    header("Location: candidatures_admin.php");
    exit;
}

$stmt = $connexion->prepare($sql);
$stmt->execute([':id' => $idjoueur]);

$_SESSION['flash_message'] = $msg;

header("Location: candidatures_admin.php");
exit;
