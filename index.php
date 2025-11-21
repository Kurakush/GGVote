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

    <h1>Jeux disponibles</h1>
    <div class="sections-jeux">
        <div class="tuiles">
            <img src="images/valorant.jpg" alt="Valorant">
            <h3>Valorant</h3>
        </div>

        <div class="tuiles" onclick="openVideo('https://www.youtube.com/embed/SwlBTktgMM4')">
            <img src="images/lol.png" alt="League of Legends">
            <h3>League of Legends</h3>
        </div>

        <div class="tuiles">
            <img src="images/csgo.jpg" alt="CSGO">
            <h3>CSGO:2</h3>
        </div>

        <div class="tuiles">
            <img src="images/fortnite1.png" alt="Fortnite">
            <h3>Fortnite</h3>
        </div>

        <div class="tuiles">
            <img src="images/rocketleague.jpg" alt="Rocket League">
            <h3>Rocket League</h3>
        </div>
    </div>

    <div id="videoModal" class="modal-video">
    <div class="modal-video-content">
        <span class="video-close" onclick="closeVideo()">&times;</span>
        <iframe id="gameVideo" width="560" height="315"
            src=""
            title="Gameplay"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen>
        </iframe>
    </div>
    </div>

    <script>
    function openVideo(url) {
        const videoId = url.split("v=")[1]; // récupère l'ID après v=
        const embedUrl = "https://www.youtube.com/embed/" + videoId;

        document.getElementById("videoModal").style.display = "flex";
        document.getElementById("gameVideo").src = url;
    }

    function closeVideo() {
        document.getElementById("videoModal").style.display = "none";
        document.getElementById("gameVideo").src = "";
    }
    </script>
</div>



<?php
require('footer.php');
?>



