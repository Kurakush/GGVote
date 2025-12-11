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

    <div class="titre-section">
    <h1>Jeux disponibles</h1>
    </div>
    <div class="sections-jeux">
        <div class="tuiles" onclick="openVideo('https://www.youtube.com/embed/vlcIs06x7A8')">
            <img src="images/valorant.jpg" alt="Valorant">
            <h3>Valorant</h3>
            <p>En savoir plus</p>
        </div>

        <div class="tuiles" onclick="openVideo('https://www.youtube.com/embed/SwlBTktgMM4')">
            <img src="images/lol.png" alt="League of Legends">
            <h3>League of Legends</h3>
            <p>En savoir plus</p>
        </div>

        <div class="tuiles" onclick="openVideo('https://www.youtube.com/embed/2S6vYJl6nkA')">
            <img src="images/csgo.jpg" alt="CSGO">
            <h3>CSGO:2</h3>
            <p>En savoir plus</p>
        </div>

        <div class="tuiles" onclick="openVideo('https://www.youtube.com/embed/yaenw0_QBhQ')">
            <img src="images/fortnite1.png" alt="Fortnite">
            <h3>Fortnite</h3>
            <p>En savoir plus</p>
        </div>

        <div class="tuiles" onclick="openVideo('https://www.youtube.com/embed/kctwBwN-ht0')">
            <img src="images/rocketleague.jpg" alt="Rocket League">
            <h3>Rocket League</h3>
            <p>En savoir plus</p>
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

    <br><br>
    
    <div class="modeVote">
        <h1>Comment est élu le meilleur joueur ?</h1>
        <p>Dans GGVote, nous avons retenu un <strong>scrutin uninominal majoritaire à un tour</strong>, un mode de vote simple, transparent et parfaitement adapté aux compétitions e-sport. </p>
        <p>Chaque électeur dispose d’un unique vote, qu’il attribue au joueur qu’il considère comme le meilleur.</p>
        <p>À la fin de la période de vote, le candidat ayant obtenu le plus grand nombre de voix est déclaré vainqueur.</p>
        <p>Ce système garantit une compréhension immédiate des résultats, une participation intuitive pour les utilisateurs, et respecte les principes fondamentaux d’un vote sécurisé : unicité, anonymat, et intégrité du dépouillement. </p>
    </div>

    <div class="cta-vote">
    <div class="cta-vote-text">
        <h2>Prêt à voter pour votre champion ?</h2>
        <p>
            Connectez-vous, choisissez votre jeu préféré et soutenez le joueur 
            qui mérite le titre de MVP. Chaque vote compte&nbsp;!
        </p>
    </div>

    <?php
    if ($electeur) { ?>
        <a href="voter.php" class="cta-vote-btn">Aller voter</a>
    <?php } 
    else { ?>
        <a href="#" class="cta-vote-btn" onclick="authenticate()">Aller voter</a>
    <?php } ?>
</div>


    <script>
    function openVideo(url) {
        // on ajoute ?autoplay=1 pour lancer la vidéo direct
        const urlAvecAutoplay = url + '?autoplay=1&rel=0';

        document.getElementById("videoModal").style.display = "flex";
        document.getElementById("gameVideo").src = urlAvecAutoplay;
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



