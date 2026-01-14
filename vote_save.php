<?php
require_once 'dbconnect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['idelecteur'])) {
    $_SESSION['flash_error'] = "Vous devez être connecté pour voter.";
    header("Location: index.php");
    exit;
}

$idelecteur = (int)$_SESSION['idelecteur'];

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

    // 1) Vérifier que le scrutin est ouvert + récupérer idcompetition
    $sqlScrutin = "SELECT s.idscrutin, s.idcompetition
                   FROM scrutin s
                   WHERE s.idscrutin = :id
                     AND s.etat_scrutin = 'ouvert'
                     AND s.date_ouverture <= :now
                     AND s.date_cloture   >= :now";
    $stmtS = $connexion->prepare($sqlScrutin);
    $stmtS->execute([':id' => $idscrutin, ':now' => $now]);
    $scrutin = $stmtS->fetch(PDO::FETCH_ASSOC);

    if (!$scrutin) {
        $_SESSION['flash_error'] = "Ce scrutin n'est pas ouvert au vote.";
        header("Location: voter.php");
        exit;
    }

    $idcompetition = (int)$scrutin['idcompetition'];

    // 2) Vérifier que le joueur appartient bien à la compétition du scrutin
    $sqlJoueur = "SELECT 1
                  FROM joueur
                  WHERE idjoueur = :idjoueur
                    AND idcompetition = :idcompetition";
    $stmtJ = $connexion->prepare($sqlJoueur);
    $stmtJ->execute([':idjoueur' => $idjoueur, ':idcompetition' => $idcompetition]);

    if (!$stmtJ->fetchColumn()) {
        $_SESSION['flash_error'] = "Le joueur choisi n'appartient pas à cette compétition.";
        header("Location: voter.php");
        exit;
    }

    // 3) Bloquer si l'électeur a déjà voté pour ce scrutin (émargement)
    $sqlAlready = "SELECT 1 FROM emargement
                   WHERE idelecteur = :idelecteur
                     AND idscrutin  = :idscrutin
                   LIMIT 1";
    $stmtA = $connexion->prepare($sqlAlready);
    $stmtA->execute([':idelecteur' => $idelecteur, ':idscrutin' => $idscrutin]);

    if ($stmtA->fetchColumn()) {
        $_SESSION['flash_error'] = "Vous avez déjà voté pour ce scrutin.";
        header("Location: voter.php");
        exit;
    }

    // 4) Vérifier le token (anonyme) : on compare aux hash de la compétition
    $sqlToken = "SELECT idtoken, token_hash
                 FROM token
                 WHERE idcompetition = :idcompetition
                   AND etat = 0";
    $stmtT = $connexion->prepare($sqlToken);
    $stmtT->execute([':idcompetition' => $idcompetition]);
    $tokens = $stmtT->fetchAll(PDO::FETCH_ASSOC);

    if (!$tokens) {
        $_SESSION['flash_error'] = "Aucun jeton valide trouvé pour cette compétition.";
        header("Location: voter.php");
        exit;
    }

    $idtoken = null;
    foreach ($tokens as $t) {
        if (!empty($t['token_hash']) && password_verify($token_code, $t['token_hash'])) {
            $idtoken = (int)$t['idtoken'];
            break;
        }
    }

    if (!$idtoken) {
        $_SESSION['flash_error'] = "Jeton invalide ou déjà utilisé.";
        header("Location: voter.php");
        exit;
    }

    // 5) Transaction : émargement + vote + invalidation token
    $connexion->beginTransaction();

    // 5a) Émargement (la contrainte UNIQUE empêche toute double insertion)
    $sqlEm = "INSERT INTO emargement (idelecteur, idscrutin, date_vote)
              VALUES (:idelecteur, :idscrutin, NOW())";
    $stmtE = $connexion->prepare($sqlEm);
    $stmtE->execute([':idelecteur' => $idelecteur, ':idscrutin' => $idscrutin]);

    // 5b) Urne : enregistrement du vote (anonyme)
    $sqlInsert = "INSERT INTO vote (date_vote, heure_vote, idscrutin, idjoueur, idtoken)
                  VALUES (CURRENT_DATE, CURRENT_TIME, :idscrutin, :idjoueur, :idtoken)";
    $stmtV = $connexion->prepare($sqlInsert);
    $stmtV->execute([':idscrutin' => $idscrutin, ':idjoueur' => $idjoueur, ':idtoken' => $idtoken]);

    // 5c) Invalidation token
    $sqlUpd = "UPDATE token SET etat = 1 WHERE idtoken = :idtoken";
    $stmtU = $connexion->prepare($sqlUpd);
    $stmtU->execute([':idtoken' => $idtoken]);

    $connexion->commit();

    unset($_SESSION['last_token']); // optionnel : on masque le token affiché

    $_SESSION['flash_message'] = "Votre vote a bien été enregistré. Merci !";
    header("Location: profil_electeur.php");
    exit;

} catch (PDOException $e) {
    if ($connexion->inTransaction()) {
        $connexion->rollBack();
    }

    // Si l'UNIQUE (idelecteur, idscrutin) bloque (double vote), on met un message clair
    $_SESSION['flash_error'] = "Vote refusé : vous avez déjà voté pour ce scrutin.";
    header("Location: voter.php");
    exit;
}
