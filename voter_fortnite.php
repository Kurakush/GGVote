<?php 
require('header.php');

$connexion = dbconnect();
if (!$connexion) {
    die("Pb d'accès à la bdd");
}

// 1) Récupérer les compétitions Fortnite
$sql = "SELECT idcompetition, nom_compet
        FROM competition
        WHERE idjeu = 4"; 
$stmt = $connexion->query($sql);
$competitions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="about-us">
    <img src="images/fortniteVoter.png" alt="fortnite" width="368" height="260">
    <h1>MVP Fortnite</h1>
    <p>Voter pour élir les MVP de Fortnite par compétitions !</p>
    <p><strong>Attention votre vote ne sera plus modifiable après la validation</strong></p>
</div>

<?php
// 2) Pour chaque compétition Fortnite, on affiche les joueurs associés
foreach ($competitions as $comp) {

    // Récupérer les joueurs associés à cette compétition
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

<?php } ?>

<?php
require('footer.php');
?>
