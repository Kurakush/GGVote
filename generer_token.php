<?php
require_once 'dbconnect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['idelecteur'])) {
    $_SESSION['flash_error'] = "Vous devez être connecté.";
    header("Location: index.php");
    exit;
}

if (empty($_GET['idcompetition'])) {
    $_SESSION['flash_error'] = "Compétition manquante.";
    header("Location: profil_electeur.php");
    exit;
}

$idelecteur    = (int)$_SESSION['idelecteur'];
$idcompetition = (int)$_GET['idcompetition'];
$force         = isset($_GET['force']) && (int)$_GET['force'] === 1;

$connexion = dbconnect();
if (!$connexion) {
    $_SESSION['flash_error'] = "Erreur de connexion à la base de données.";
    header("Location: profil_electeur.php");
    exit;
}

try {
    // 1) Vérifier qu'il existe AU MOINS un scrutin ouvert pour cette compétition
    $sqlOpen = "SELECT 1
                FROM scrutin
                WHERE idcompetition = :idcompetition
                  AND etat_scrutin = 'ouvert'
                  AND date_ouverture <= NOW()
                  AND date_cloture   >= NOW()
                LIMIT 1";
    $stmtO = $connexion->prepare($sqlOpen);
    $stmtO->execute([':idcompetition' => $idcompetition]);

    if (!$stmtO->fetchColumn()) {
        $_SESSION['flash_error'] = "Aucun scrutin ouvert pour cette compétition.";
        header("Location: profil_electeur.php");
        exit;
    }

    // 2) BLOQUAGE : si l'électeur a déjà voté pour un scrutin OUVERT de cette compétition
    // (émargement = preuve de participation, sans révéler le choix)
    $sqlEm = "SELECT 1
              FROM emargement e
              JOIN scrutin s ON s.idscrutin = e.idscrutin
              WHERE e.idelecteur = :idelecteur
                AND s.idcompetition = :idcompetition
                AND s.etat_scrutin = 'ouvert'
                AND s.date_ouverture <= NOW()
                AND s.date_cloture   >= NOW()
              LIMIT 1";
    $stmtE = $connexion->prepare($sqlEm);
    $stmtE->execute([
        ':idelecteur'    => $idelecteur,
        ':idcompetition' => $idcompetition
    ]);

    if ($stmtE->fetchColumn()) {
        $_SESSION['flash_error'] = "Vous avez déjà voté pour ce scrutin. Génération de token refusée.";
        header("Location: profil_electeur.php");
        exit;
    }

    // 3) Si on a déjà un token en session pour cette compétition, on le ré-affiche
    if (!empty($_SESSION['last_token']['code']) && (int)$_SESSION['last_token']['idcompetition'] === $idcompetition) {
        $_SESSION['flash_message'] = "Votre token est déjà disponible dans la section dédiée.";
        header("Location: profil_electeur.php");
        exit;
    }

    // 4) Existe-t-il un token non utilisé en BDD pour cette compétition ?
    $sqlCheck = "SELECT COUNT(*)
                 FROM token
                 WHERE idcompetition = :idcompetition
                   AND etat = 0";
    $stmtC = $connexion->prepare($sqlCheck);
    $stmtC->execute([':idcompetition' => $idcompetition]);
    $nb = (int)$stmtC->fetchColumn();

    if ($nb > 0 && !$force) {
        $_SESSION['flash_error'] =
            "Un token non utilisé existe déjà pour cette compétition, mais il n'est pas récupérable (stocké uniquement sous forme de hash). "
          . "Cliquez sur « Regénérer » pour invalider l'ancien token et en créer un nouveau.";

        $_SESSION['regen_compet'] = $idcompetition;

        header("Location: profil_electeur.php");
        exit;
    }

    // 5) Si force=1 et qu'il existe déjà un token non utilisé, on l'invalide
    if ($nb > 0 && $force) {
        $sqlInvalidate = "UPDATE token
                          SET etat = 1
                          WHERE idcompetition = :idcompetition
                            AND etat = 0";
        $stmtInv = $connexion->prepare($sqlInvalidate);
        $stmtInv->execute([':idcompetition' => $idcompetition]);
    }

    // 6) Génération d'un nouveau token
    $token_code = bin2hex(random_bytes(16));
    $token_hash = password_hash($token_code, PASSWORD_DEFAULT);

    $sqlInsert = "INSERT INTO token (token_hash, idcompetition, etat, date_generation)
                  VALUES (:hash, :idcompetition, 0, NOW())";
    $stmtI = $connexion->prepare($sqlInsert);
    $stmtI->execute([
        ':hash' => $token_hash,
        ':idcompetition' => $idcompetition
    ]);

    $_SESSION['last_token'] = [
        'code' => $token_code,
        'idcompetition' => $idcompetition,
        'created_at' => date('Y-m-d H:i:s')
    ];

    unset($_SESSION['regen_compet']);

    $_SESSION['flash_message'] = "Token généré ! Vous pouvez le copier dans la section dédiée.";
    header("Location: profil_electeur.php");
    exit;

} catch (PDOException $e) {
    // error_log($e->getMessage());
    $_SESSION['flash_error'] = "Erreur lors de la génération du token.";
    header("Location: profil_electeur.php");
    exit;
}
