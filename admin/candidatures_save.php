<?php
require "check_admin.php";
require "../dbconnect.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: candidatures_admin.php");
    exit;
}

$idjoueur = isset($_POST['idjoueur']) ? (int)$_POST['idjoueur'] : 0;
$action   = $_POST['action'] ?? '';

if ($idjoueur <= 0 || !in_array($action, ['valider', 'refuser'])) {
    header("Location: candidatures_admin.php");
    exit;
}

$connexion = dbconnect();
if (!$connexion) {
    die("Erreur d'accès à la base de données.");
}

if ($action === 'valider') {

    // Validation définitive
    $sql = "UPDATE joueur
            SET candidature_validee = 1,
                candidature_complete = 1
            WHERE idjoueur = :id";
    $message = "La candidature a été validée.";

} elseif ($action === 'refuser') {

    // Refus → le candidat pourra corriger et renvoyer
    $sql = "UPDATE joueur
            SET candidature_validee = 0,
                candidature_complete = 0
            WHERE idjoueur = :id";
    $message = "La candidature a été refusée.";

}

$stmt = $connexion->prepare($sql);
$stmt->execute([':id' => $idjoueur]);

// Message de confirmation
$_SESSION['flash_message'] = $message;

header("Location: candidatures_admin.php");
exit;
