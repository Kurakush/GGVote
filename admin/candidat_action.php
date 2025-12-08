<?php
require "check_admin.php";
require "../dbconnect.php";

$connexion = dbconnect();
if (!$connexion) {
    die("Pb d'accès à la bdd");
}

$id     = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$action = $_GET['action'] ?? '';

if ($id > 0 && $action === 'supprimer') {
    $stmt = $connexion->prepare("DELETE FROM joueur WHERE idjoueur = ?");
    $stmt->execute([$id]);
}

header("Location: candidats.php");
exit;
