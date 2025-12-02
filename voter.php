<?php 
require('header.php');
?>

<?php
session_start();
if (
    !isset($_SESSION["electeur_email"]) &&   // pas électeur
    !isset($_SESSION["login"])               // pas admin
) {
    header("Location: index.php");
    exit;
}
?>

<div class="about-us">
        <h1>Comment voter ?</h1>
        <p>En tant qu'électeur vous posséderez un jeton de vote par compétition. Pour voter choisissez simplement la compétition qui vous intérresse, puis voter pour votre joueur(euse) favoris.
        </p>

        <p>Notre système garantit un vote <strong>unique</strong>, <strong>anonyme</strong> et
        <strong>sécurisé</strong> afin d'assurer un classement transparent et fiable. Que vous soyez
        visiteur ou électeur, GGVote vous offre une expérience simple et intuitive.
        </p>
    </div>

<h1>Jeux disponibles</h1>

<div class="sections-jeux">

    <a href="voter_valo.php" class="tuiles">
        <img src="images/valorant.jpg" alt="Valorant">
        <h3>Valorant</h3>
    </a>

    <a href="voter_lol.php" class="tuiles">
        <img src="images/lol.png" alt="League of Legends">
        <h3>League of Legends</h3>
    </a>

    <a href="voter_csgo.php" class="tuiles">
        <img src="images/csgo.jpg" alt="CSGO">
        <h3>CSGO:2</h3>
    </a>

    <a href="voter_fortnite.php" class="tuiles">
        <img src="images/fortnite1.png" alt="Fortnite">
        <h3>Fortnite</h3>
    </a>

    <a href="voter_rl.php" class="tuiles">
        <img src="images/rocketleague.jpg" alt="Rocket League">
        <h3>Rocket League</h3>
    </a>

</div>

<?php 
require('footer.php');
?>