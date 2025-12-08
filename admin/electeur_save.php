<?php
require "check_admin.php";
require "../dbconnect.php";

$connexion = dbconnect();
if (!$connexion) {
    die("Pb d'accès à la bdd");
}

$mode       = $_POST['mode'] ?? 'ajout';
$email      = $_POST['email'] ?? '';
$password   = $_POST['password'] ?? '';
$type       = $_POST['type'] ?? 'ELECTEUR';

if ($mode === "ajout") {

    $sql = "INSERT INTO electeur (email, mot_de_passe, type, actif)
            VALUES (:email, :mdp, :type, 1)";
    $stmt = $connexion->prepare($sql);
    $stmt->execute([
        ':email' => $email,
        ':mdp'   => $password,   // compat avec ton login actuel
        ':type'  => $type
    ]);

} else { // edition
    $idelecteur = (int) ($_POST['idelecteur'] ?? 0);

    if ($password !== "") {
        // on met à jour email + mdp
        $sql = "UPDATE electeur
                SET email = :email,
                    mot_de_passe = :mdp,
                    type = :type
                WHERE idelecteur = :id";
        $stmt = $connexion->prepare($sql);
        $stmt->execute([
            ':email' => $email,
            ':mdp'   => $password,
            ':type'  => $type,
            ':id'    => $idelecteur
        ]);
    } else {
        // on laisse le mot de passe inchangé
        $sql = "UPDATE electeur
                SET email = :email,
                    type = :type
                WHERE idelecteur = :id";
        $stmt = $connexion->prepare($sql);
        $stmt->execute([
            ':email' => $email,
            ':type'  => $type,
            ':id'    => $idelecteur
        ]);
    }
}

header("Location: electeurs.php");
exit;
