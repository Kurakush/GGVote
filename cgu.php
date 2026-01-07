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
    <h1>Conditions Générales d’Utilisation</h1>

    <section>
        <h2>Objet</h2>
        <p>
            Les présentes Conditions Générales d’Utilisation ont pour objet
            de définir les modalités d’accès et d’utilisation du site GGVote.
        </p>
    </section>

    <section>
        <h2>Accès au service</h2>
        <p>
            Le site est accessible gratuitement aux utilisateurs disposant
            d’un accès Internet.
        </p>
    </section>

    <section>
        <h2>Responsabilités</h2>
        <p>
            GGVote ne saurait être tenu responsable en cas de dysfonctionnement
            du service ou d’interruption temporaire.
        </p>
    </section>

    <section>
        <h2>Propriété intellectuelle</h2>
        <p>
            L’ensemble du site (structure, contenus, textes) est protégé
            par le droit de la propriété intellectuelle.
        </p>
    </section>
</main>

<?php include('footer.php'); ?>
