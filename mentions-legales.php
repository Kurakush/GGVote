<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'header.php';

$connexion = dbconnect();
if (!$connexion) {
    die("Pb d'accès à la base de données.");
}
?>

<main class="legal-container">
    <h1>Mentions légales</h1>

    <section>
        <h2>Éditeur du site</h2>
        <p>
            Nom du projet : <strong>GGVote</strong><br>
            Projet réalisé dans le cadre d’un projet pédagogique (SAÉ S3).<br>
            Établissement : [Nom de ton IUT / Université]
        </p>
    </section>

    <section>
        <h2>Responsable de la publication</h2>
        <p>
            [Nom(s) des étudiants]
        </p>
    </section>

    <section>
        <h2>Hébergement</h2>
        <p>
            Site hébergé en local via WampServer.<br>
            Aucun hébergement public.
        </p>
    </section>

    <section>
        <h2>Données personnelles</h2>
        <p>
            Les données collectées sont utilisées uniquement dans le cadre du
            fonctionnement du système de vote GGVote et ne sont pas transmises
            à des tiers.
        </p>
    </section>
</main>

<?php include('footer.php'); ?>
