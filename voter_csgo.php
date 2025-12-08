<?php 
require('header.php');

$connexion = dbconnect();
if (!$connexion) {
    die("Pb d'accès à la bdd");
}

/* --------- Vérifier si au moins un scrutin CSGO est OUVERT --------- */
$idjeu = 5;
$now   = date('Y-m-d H:i:s');

$sqlCheck = "SELECT COUNT(*)
             FROM scrutin s
             JOIN competition c ON c.idcompetition = s.idcompetition
             WHERE c.idjeu = :idjeu
               AND s.etat_scrutin = 'ouvert'
               AND s.date_ouverture <= :now
               AND s.date_cloture   >= :now";
$stmtCheck = $connexion->prepare($sqlCheck);
$stmtCheck->execute([
    ':idjeu' => $idjeu,
    ':now'   => $now
]);
$nbScrutinsOuverts = (int)$stmtCheck->fetchColumn();

if ($nbScrutinsOuverts === 0) {
    // aucun scrutin LoL ouvert → on affiche un message et on arrête
    ?>
    <div class="scrutin-info">
        <h2>Scrutin fermé</h2>
        <p>Aucun vote n'est actuellement ouvert pour CSGO:2.</p>
        <p>Revenez plus tard ou choisissez un autre jeu.</p>
    </div>
    <?php
    require('footer.php');
    exit;
}

// Récupérer les compétitions CS2 d'un scrutin ouvert
$sql = "SELECT DISTINCT c.idcompetition, c.nom_compet, s.idscrutin
        FROM competition c
        JOIN scrutin s ON s.idcompetition = c.idcompetition
        WHERE c.idjeu = :idjeu
          AND s.etat_scrutin = 'ouvert'
          AND s.date_ouverture <= :now
          AND s.date_cloture   >= :now";
$stmt = $connexion->prepare($sql);
$stmt->execute([
    ':idjeu' => 5,     // idjeu CSGO2
    ':now'   => $now
]);
$competitions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="about-us">
    <img src="images/csgo.jpg" alt="CS2" width="300" height="200">
    <h1>MVP CSGO:2</h1>
    <p>Voter pour élire le MVP de CSGO:2 par compétitions !</p>
    <p><strong>Attention, votre vote ne sera plus modifiable après la validation.</strong></p>
</div>

<?php
// 2) Pour chaque compétition CS2, on affiche les joueurs associés
foreach ($competitions as $comp) {

    // Joueurs de cette compétition
    $sqlJ = "SELECT *
             FROM joueur
             WHERE idcompetition = :idcomp
             ORDER BY pseudo";
    $stmtJ = $connexion->prepare($sqlJ);
    $stmtJ->execute([':idcomp' => $comp['idcompetition']]);
    $joueurs = $stmtJ->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="compet">
        <h2><?= htmlspecialchars($comp['nom_compet']) ?></h2>
    </div>

    <?php if (count($joueurs) === 0): ?>
        <p style="text-align:center;margin-bottom:40px;">
            Aucun joueur enregistré pour cette compétition.
        </p>
    <?php else: ?>
        <div class="sections-jeux">
            <?php foreach ($joueurs as $j): ?>
                <div class="tuiles">
                    <?php if (!empty($j['photo'])): ?>
                        <img src="images/<?= htmlspecialchars($j['photo']) ?>"
                             alt="<?= htmlspecialchars($j['pseudo']) ?>">
                    <?php endif; ?>
                    <h3><?= strtoupper(htmlspecialchars($j['pseudo'])) ?></h3>
                    <p>Joueur chez <?= htmlspecialchars($j['equipe']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

<?php } // fin foreach compétitions ?>

<?php
require('footer.php');
?>
