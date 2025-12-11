<?php
require "check_admin.php";
require "../dbconnect.php";

$idadmin = $_SESSION['admin_id'] ?? null;

$connexion = dbconnect();
if (!$connexion) {
    die("Pb d'accès à la bdd");
}

/*
 * 1) Récupérer les scrutins FERMÉS
 *    (adapter la valeur de etat_scrutin si tu utilises autre chose que 'clos')
 */
$sqlScrutins = "SELECT idscrutin, nom_scrutin
                FROM scrutin
                WHERE etat_scrutin = 'cloturer'";
$stmtScrutins = $connexion->query($sqlScrutins);
$scrutins = $stmtScrutins->fetchAll(PDO::FETCH_ASSOC);

if (empty($scrutins)) {
    $_SESSION['flash_message'] = "Aucun scrutin clos à traiter.";
    header("Location: scrutins.php");
    exit;
}

/*
 * 2) Pour chaque scrutin clos, on recalcule les résultats
 */
foreach ($scrutins as $scrutin) {
    $idscrutin = (int)$scrutin['idscrutin'];

    // a) On efface d'abord les anciens résultats de ce scrutin
    $sqlDel = "DELETE FROM resultat WHERE idscrutin = :idscrutin";
    $stmtDel = $connexion->prepare($sqlDel);
    $stmtDel->execute([':idscrutin' => $idscrutin]);

    // b) On regroupe les votes par joueur
    //    adapte le nom de ta table de votes et des colonnes si besoin
    $sqlVotes = "SELECT idjoueur, COUNT(*) AS nb_votes
                 FROM vote
                 WHERE idscrutin = :idscrutin
                 GROUP BY idjoueur
                 ORDER BY nb_votes DESC";
    $stmtVotes = $connexion->prepare($sqlVotes);
    $stmtVotes->execute([':idscrutin' => $idscrutin]);
    $rows = $stmtVotes->fetchAll(PDO::FETCH_ASSOC);

    // c) On insère dans la table resultat avec un rang
    $sqlInsert = "INSERT INTO resultat (nb_votes, date_calcul, rang, idadmin, idscrutin, idjoueur)
                  VALUES (:nb_votes, NOW(), :rang, :idadmin, :idscrutin, :idjoueur)";
    $stmtInsert = $connexion->prepare($sqlInsert);

    $rang = 1;
    foreach ($rows as $row) {
        $stmtInsert->execute([
            ':nb_votes' => (int)$row['nb_votes'],
            ':rang'     => $rang,
            ':idadmin'  => $idadmin,
            ':idscrutin'=> $idscrutin,
            ':idjoueur' => (int)$row['idjoueur']
        ]);
        $rang++;
    }
}

$_SESSION['flash_message'] = "Résultats recalculés pour tous les scrutins clos.";
header("Location: scrutins.php");
exit;
