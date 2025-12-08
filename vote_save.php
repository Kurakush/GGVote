<?php
// Pas de header.php ici : on ne veut PAS envoyer de HTML
require_once 'dbconnect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// L'utilisateur doit être électeur connecté
if (!isset($_SESSION['idelecteur'])) {
    $_SESSION['flash_error'] = "Vous devez être connecté pour voter.";
    header("Location: index.php");
    exit;
}

$idelecteur = (int)$_SESSION['idelecteur'];

// Vérifier les champs envoyés par le formulaire
if (
    empty($_POST['idscrutin']) ||
    empty($_POST['idjoueur'])  ||
    empty($_POST['token_code'])
) {
    $_SESSION['flash_error'] = "Formulaire de vote incomplet.";
    header("Location: voter.php");
    exit;
}

$idscrutin  = (int)$_POST['idscrutin'];
$idjoueur   = (int)$_POST['idjoueur'];
$token_code = trim($_POST['token_code']);

$connexion = dbconnect();
if (!$connexion) {
    $_SESSION['flash_error'] = "Erreur de connexion à la base de données.";
    header("Location: voter.php");
    exit;
}

try {
    $now = date('Y-m-d H:i:s');

    // 1) Vérifier que le scrutin est ouvert (dates + etat_scrutin = 1)
    $sqlScrutin = "SELECT s.*, c.idcompetition
                   FROM scrutin s
                   JOIN competition c ON c.idcompetition = s.idcompetition
                   WHERE s.idscrutin = :id
                     AND s.etat_scrutin = 'ouvert'
                     AND s.date_ouverture <= :now
                     AND s.date_cloture   >= :now";
    $stmtS = $connexion->prepare($sqlScrutin);
    $stmtS->execute([
        ':id'  => $idscrutin,
        ':now' => $now
    ]);
    $scrutin = $stmtS->fetch(PDO::FETCH_ASSOC);

    if (!$scrutin) {
        $_SESSION['flash_error'] = "Ce scrutin n'est pas ouvert au vote.";
        header("Location: voter.php");
        exit;
    }

    $idcompetition = (int)$scrutin['idcompetition'];

    // 2) Vérifier que le joueur appartient bien à la compétition du scrutin
    $sqlJoueur = "SELECT idjoueur
                  FROM joueur
                  WHERE idjoueur = :idjoueur
                    AND idcompetition = :idcompetition";
    $stmtJ = $connexion->prepare($sqlJoueur);
    $stmtJ->execute([
        ':idjoueur'     => $idjoueur,
        ':idcompetition'=> $idcompetition
    ]);
    $joueurOk = $stmtJ->fetch(PDO::FETCH_ASSOC);

    if (!$joueurOk) {
        $_SESSION['flash_error'] = "Le joueur choisi n'appartient pas à ce scrutin.";
        header("Location: voter.php");
        exit;
    }

    // 3) Vérifier le token : appartient à l'électeur, bon scrutin, pas utilisé
    $sqlToken = "SELECT *
                 FROM token
                 WHERE code_token = :code
                   AND idelecteur = :idelecteur
                   AND idscrutin  = :idscrutin
                   AND utilise    = 0";
    $stmtT = $connexion->prepare($sqlToken);
    $stmtT->execute([
        ':code'       => $token_code,
        ':idelecteur' => $idelecteur,
        ':idscrutin'  => $idscrutin
    ]);
    $token = $stmtT->fetch(PDO::FETCH_ASSOC);

    if (!$token) {
        $_SESSION['flash_error'] = "Jeton invalide, déjà utilisé ou ne correspondant pas à ce scrutin.";
        header("Location: voter.php");
        exit;
    }

    $idtoken = (int)$token['idtoken'];

    // 4) Enregistrer le vote + marquer le token utilisé dans une transaction
    $connexion->beginTransaction();

    $sqlInsert = "INSERT INTO vote (date_vote, heure_vote, idscrutin, idjoueur, idtoken)
                  VALUES (CURRENT_DATE, CURRENT_TIME, :idscrutin, :idjoueur, :idtoken)";
    $stmtV = $connexion->prepare($sqlInsert);
    $stmtV->execute([
        ':idscrutin' => $idscrutin,
        ':idjoueur'  => $idjoueur,
        ':idtoken'   => $idtoken
    ]);

    $sqlUpd = "UPDATE token
               SET utilise = 1
               WHERE idtoken = :idtoken";
    $stmtU = $connexion->prepare($sqlUpd);
    $stmtU->execute([':idtoken' => $idtoken]);

    $connexion->commit();

    $_SESSION['flash_message'] = "Votre vote a bien été enregistré. Merci pour votre participation !";
    // Redirection vers le profil pour voir le récap
    header("Location: profil_electeur.php");
    exit;

} catch (PDOException $e) {

    if ($connexion->inTransaction()) {
        $connexion->rollBack();
    }

    // En debug tu peux var_dump($e->getMessage());
    $_SESSION['flash_error'] = "Une erreur est survenue lors de l'enregistrement du vote.";
    header("Location: voter.php");
    exit;
}
