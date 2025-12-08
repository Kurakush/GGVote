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

    if ($action === 'ouvrir') {
        $stmt = $connexion->prepare("UPDATE scrutin SET etat_scrutin = 'ouvert' WHERE idscrutin = ?");
        $stmt->execute([$id]);

    } elseif ($action === 'cloturer') {
        $stmt = $connexion->prepare("UPDATE scrutin SET etat_scrutin = 'cloture' WHERE idscrutin = ?");
        $stmt->execute([$id]);

    } elseif ($action === 'supprimer') {
        $stmt = $connexion->prepare("DELETE FROM scrutin WHERE idscrutin = ?");
        $stmt->execute([$id]);
    }
}

header("Location: scrutins.php");
exit;
