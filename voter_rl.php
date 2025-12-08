<?php 
require('header.php');

$connexion = dbconnect();
if (!$connexion) {
    die("Pb d'accès à la bdd");
}

// 1) Récupérer les compétitions Rocket League
$sql = "SELECT idcompetition, nom_compet
        FROM competition
        WHERE idjeu = 3 ";
$stmt = $connexion->query($sql);
$competitions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="about-us">
    <img src="images/rocketleague.jpg" alt="RL" width="384" height="216">
    <h1>MVP Rocket League</h1>
    <p>Voter pour élire le MVP de Rocket League par compétitions !</p>
    <p><strong>Attention votre vote ne sera plus modifiable après la validation</strong></p>
</div>

<?php
// 2) Pour chaque compétition RL, on affiche les joueurs associés
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

                    <h3><?= strtolower(htmlspecialchars($j['pseudo'])) ?></h3>
                    <p> Joueur chez <?= htmlspecialchars($j['equipe']) ?> </p>

                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

<?php } ?>

<?php
require('footer.php');
?>
