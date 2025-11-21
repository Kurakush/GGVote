<?php 
require('header.php');
?>

<div class="main">
    <div class="slider-container">
        <div class="carousel">
            <label for="slide-dot-1"></label>
            <label for="slide-dot-2"></label>
            <label for="slide-dot-3"></label>
        </div>
        <input id="slide-dot-1" type="radio" name="slides" checked>
        <div class="slide slide-1"></div>
        <input id="slide-dot-2" type="radio" name="slides">
        <div class="slide slide-2"></div>
        <input id="slide-dot-3" type="radio" name="slides">
        <div class="slide slide-3"></div>
    </div>

    <div class="about-us">
        <h1>A propos de GGVote</h1>
        <p>GGVote est une plateforme de vote en ligne dédiée aux compétitions e-sport.
            Elle permet aux fans de voter pour les meilleurs joueurs de chaque compétition majeure
            dans différents jeux tels que League of Legends, Valorant, CSGO:2 et bien d'autres.
        </p>

        <p>Notre système garantit un vote <strong>unique</strong>, <strong>anonyme</strong> et
        <strong>sécurisé</strong> afin d'assurer un classement transparent et fiable. Que vous soyez
        visiteur ou électeur, GGVote vous offre une expérience simple et intuitive.
        </p>
    </div>
</div>



<?php
require('footer.php');
?>



