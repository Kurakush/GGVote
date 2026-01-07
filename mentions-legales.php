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

  <header class="legal-header">
    <h1>Mentions légales</h1>
    <p class="legal-subtitle">
      Informations légales relatives à l’application GGVote.
    </p>
  </header>

  <section class="legal-section">
    <h2>1. Cadre légal</h2>
    <p>
      Conformément aux obligations légales applicables aux services numériques et à la loi de confiance dans l’économie numérique,
      l’application GGVote doit comporter des mentions légales accessibles aux utilisateurs.
    </p>
    <p>
      Les mentions légales permettent d’identifier le responsable de l’application, de préciser le cadre dans lequel le site est exploité
      ainsi que les modalités générales de traitement des données personnelles.
    </p>
  </section>

  <section class="legal-section">
    <h2>2. Éditeur de l’application</h2>

    <div class="legal-card">
      <p><strong>Projet :</strong> GGVote (application pédagogique – SAE)</p>
      <p><strong>Responsables de l’édition et du traitement :</strong> DEGRELLE Thomas &amp; LACROIX Eve</p>
      <p><strong>Email :</strong> <a href="mailto:contactGGVote@ggvote.fr">contactGGVote@ggvote.fr</a></p>
      <p><strong>Téléphone :</strong> <a href="tel:+33329000000">03 29 00 00 00</a></p>
    </div>

    <p>
      Dans le cadre de ce projet, GGVote est une application développée à des fins pédagogiques dans le contexte d’une SAE.
    </p>
  </section>

  <section class="legal-section">
    <h2>3. Hébergement</h2>
    <div class="legal-card">
      <p><strong>Hébergement :</strong> serveur local via WampServer</p>
      <p><strong>Exploitation :</strong> sans exploitation commerciale</p>
    </div>
  </section>

  <section class="legal-section">
    <h2>4. Données personnelles</h2>
    <p>
      Les données collectées via l’application sont utilisées exclusivement pour le fonctionnement du système de vote électronique.
      Aucune donnée n’est cédée ou vendue à des tiers.
    </p>
    <p>
      Les utilisateurs disposent de moyens de contact leur permettant d’exercer leurs droits relatifs à leurs données personnelles.
    </p>
  </section>

  <section class="legal-section">
    <h2>5. Disponibilité du service</h2>
    <p>
      L’application GGVote est accessible à tout moment. Toutefois, l’accès au site peut être suspendu temporairement
      pour des raisons de maintenance ou de problèmes techniques.
    </p>
  </section>

  <section class="legal-section">
    <h2>6. Cookies</h2>
    <p>
      L’application GGVote peut être amenée à utiliser des cookies strictement nécessaires à son fonctionnement,
      notamment afin d’assurer la gestion des sessions utilisateurs et la sécurisation de l’accès au service.
    </p>
    <p>
      Conformément au droit français et aux recommandations de la CNIL, seuls les cookies techniques indispensables
      au fonctionnement de l’application sont utilisés. Ces cookies ne nécessitent pas le consentement préalable de l’utilisateur.
    </p>
  </section>

  <footer class="legal-footer">
    <p>Dernière mise à jour : <strong>07/01/2026</strong></p>
  </footer>

</main>

<?php include('footer.php'); ?>
