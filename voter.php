<?php
require("header.php");
$connexion = dbconnect();


// Accès réservé : électeur OU admin
if (
    !isset($_SESSION["electeur_email"]) &&   // pas électeur
    !isset($_SESSION["login"])               // pas admin
) {
    header("Location: index.php");
    exit;
}

// ========================
// fonction utilitaire : y a-t-il un scrutin OUVERT pour ce jeu ?
// ========================
function hasOpenScrutin(PDO $connexion, int $idjeu): bool {
    $now = date('Y-m-d H:i:s');

    $sql = "SELECT COUNT(*) 
            FROM scrutin s
            JOIN competition c ON c.idcompetition = s.idcompetition
            WHERE c.idjeu = :idjeu
              AND s.date_ouverture <= :now
              AND s.date_cloture   >= :now";
    $stmt = $connexion->prepare($sql);
    $stmt->execute([
        ':idjeu' => $idjeu,
        ':now'   => $now
    ]);
    return (int)$stmt->fetchColumn() > 0;
}

// adapte les idjeu à ta BDD
$openValo     = hasOpenScrutin($connexion, 1);
$openLoL      = hasOpenScrutin($connexion, 2);
$openCSGO     = hasOpenScrutin($connexion, 5);
$openFortnite = hasOpenScrutin($connexion, 4);
$openRL       = hasOpenScrutin($connexion, 3);


?>

<div class="about-us">
    <h1>Comment voter ?</h1>
    <p>En tant qu'électeur vous posséderez un jeton de vote par compétition. 
       Pour voter, choisissez simplement la compétition qui vous intéresse, 
       puis votez pour votre MVP.</p>

    <p>Notre système garantit un vote <strong>unique</strong>, <strong>anonyme</strong> et
       <strong>sécurisé</strong> afin d'assurer un classement transparent et fiable.</p>
</div>

<div class="titre-section">
    <h1>Jeux disponibles</h1>
</div>

<div class="sections-jeux">

    <!-- VALORANT -->
    <?php if ($openValo): ?>
        <a href="voter_valo.php" class="tuiles">
            <img src="images/valorant.jpg" alt="Valorant">
            <h3>Valorant</h3>
        </a>
    <?php else: ?>
        <div class="tuiles tuiles-disabled">
            <img src="images/valorant.jpg" alt="Valorant">
            <h3>Valorant</h3>
            <p class="tuiles-status">Aucun vote ouvert</p>
        </div>
    <?php endif; ?>

    <!-- LOL -->
    <?php if ($openLoL): ?>
        <a href="voter_lol.php" class="tuiles">
            <img src="images/lol.png" alt="League of Legends">
            <h3>League of Legends</h3>
        </a>
    <?php else: ?>
        <div class="tuiles tuiles-disabled">
            <img src="images/lol.png" alt="League of Legends">
            <h3>League of Legends</h3>
            <p class="tuiles-status">Aucun vote ouvert</p>
        </div>
    <?php endif; ?>

    <!-- CS2 -->
    <?php if ($openCSGO): ?>
        <a href="voter_csgo.php" class="tuiles">
            <img src="images/csgo.jpg" alt="CSGO">
            <h3>CSGO:2</h3>
        </a>
    <?php else: ?>
        <div class="tuiles tuiles-disabled">
            <img src="images/csgo.jpg" alt="CSGO">
            <h3>CSGO:2</h3>
            <p class="tuiles-status">Aucun vote ouvert</p>
        </div>
    <?php endif; ?>

    <!-- FORTNITE -->
    <?php if ($openFortnite): ?>
        <a href="voter_fortnite.php" class="tuiles">
            <img src="images/fortnite1.png" alt="Fortnite">
            <h3>Fortnite</h3>
        </a>
    <?php else: ?>
        <div class="tuiles tuiles-disabled">
            <img src="images/fortnite1.png" alt="Fortnite">
            <h3>Fortnite</h3>
            <p class="tuiles-status">Aucun vote ouvert</p>
        </div>
    <?php endif; ?>

    <!-- ROCKET LEAGUE -->
    <?php if ($openRL): ?>
        <a href="voter_rl.php" class="tuiles">
            <img src="images/rocketleague.jpg" alt="Rocket League">
            <h3>Rocket League</h3>
        </a>
    <?php else: ?>
        <div class="tuiles tuiles-disabled">
            <img src="images/rocketleague.jpg" alt="Rocket League">
            <h3>Rocket League</h3>
            <p class="tuiles-status">Aucun vote ouvert</p>
        </div>
    <?php endif; ?>

</div>

<?php 
require('footer.php');
?>
