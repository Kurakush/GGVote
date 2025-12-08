<?php 
require('header.php');


$connexion = dbconnect();
if (!$connexion) {
    die("Pb d'accès à la bdd");
}

// 1) Récupérer les compétitions Valorant
// Adapte la condition à ta table : soit par nom, soit par idjeu, etc.
$sql = "SELECT idcompetition, nom_compet
        FROM competition
        WHERE idjeu = 1"; // supposons que 1 = Valorant
$stmt = $connexion->query($sql);
$competitions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="about-us">
    <img src="images/valorant.jpg" alt="Valorant" width="200" height="200">
    <h1>MVP Valorant</h1>
    <p>Voter pour élir le MVP de Valorant par compétitions !</p>
    <p><strong>Attention votre vote ne sera plus modifiable après la validation</strong></p>
</div>

<?php
// Pour chaque compétition Valorant, on affiche les joueurs
foreach ($competitions as $comp) {

    // Récupérer les joueurs de cette compétition
    $sqlJ = "SELECT * FROM joueur
             WHERE idcompetition = :idcomp
             ORDER BY pseudo";
    $stmtJ = $connexion->prepare($sqlJ);
    $stmtJ->execute([':idcomp' => $comp['idcompetition']]);
    $joueurs = $stmtJ->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="compet">
        <h2><?= htmlspecialchars($comp['nom_competition']) ?></h2>
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
