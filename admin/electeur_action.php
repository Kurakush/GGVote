<?php
require "check_admin.php";
require "../dbconnect.php";

$idelecteur = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$action     = $_GET['action'] ?? '';

if ($idelecteur <= 0 || !in_array($action, ['activer', 'desactiver', 'supprimer'], true)) {
    header("Location: electeurs.php");
    exit;
}

$connexion = dbconnect();
if (!$connexion) {
    die("Pb d'accès à la bdd");
}

if ($action === 'activer') {

    $sql = "UPDATE electeur
            SET actif = 1
            WHERE idelecteur = :id";
    $msg = "Électeur activé (compte validé).";

} elseif ($action === 'desactiver') {

    $sql = "UPDATE electeur
            SET actif = 0
            WHERE idelecteur = :id";
    $msg = "Électeur désactivé.";

} elseif ($action === 'supprimer') {

    $sql = "DELETE FROM electeur
            WHERE idelecteur = :id";
    $msg = "Électeur supprimé.";
}

$stmt = $connexion->prepare($sql);
$stmt->execute([':id' => $idelecteur]);

$_SESSION['flash_message'] = $msg;

header("Location: electeurs.php");
exit;
