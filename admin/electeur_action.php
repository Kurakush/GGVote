<?php
require "check_admin.php";
require "../dbconnect.php";

$connexion = dbconnect();
if (!$connexion) {
    die("Pb d'accès à la bdd");
}

$id     = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$action = $_GET['action'] ?? '';

if ($id > 0 && $action) {

    if ($action === 'activer') {
        $stmt = $connexion->prepare("UPDATE electeur SET actif = 1 WHERE idelecteur = ?");
        $stmt->execute([$id]);

    } elseif ($action === 'desactiver') {
        $stmt = $connexion->prepare("UPDATE electeur SET actif = 0 WHERE idelecteur = ?");
        $stmt->execute([$id]);

    } elseif ($action === 'supprimer') {
        $stmt = $connexion->prepare("DELETE FROM electeur WHERE idelecteur = ?");
        $stmt->execute([$id]);
    }
}

header("Location: electeurs.php");
exit;
