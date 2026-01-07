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

<div class="cgu-page">

    <main class="legal-container">
        <header class="legal-header">
            <h1>Conditions Générales d’Utilisation</h1>
            <p class="legal-subtitle">
                Règles d’utilisation de l’application GGVote.
            </p>
        </header>

        <section>
            <p>
            Les Conditions Générales d’Utilisation ont pour objet de définir les modalités d’accès et d’utilisation de l’application GGVote, ainsi que les droits et obligations des utilisateurs.
            </p>
        </section>

        <section class="legal-section">
            <h2>Article 1 - Données à caractère personnel</h2>
            <p>
                Dans le cadre de l’utilisation de l’application GGVote, des données à caractère
                personnel peuvent être collectées, notamment des données d’identification
                (nom, prénom, adresse électronique), des données de connexion
                (identifiant, mot de passe chiffré) ainsi que des données relatives au rôle
                de l’utilisateur (électeur, candidat ou administrateur).
            </p>

            <p>
                Ces données sont collectées et traitées exclusivement dans le but d’assurer
                l’organisation, la gestion et la sécurisation du processus de vote électronique,
                conformément au Règlement Général sur la Protection des Données (RGPD)
                et à la législation en vigueur.
            </p>
            
            <p>
                Les données personnelles sont conservées pour une durée limitée et proportionnée
                aux finalités du traitement, fixée à un maximum de trente-six (36) mois.
                Elles peuvent être supprimées avant ce délai lorsqu’elles ne sont plus nécessaires
                à leur finalité.
            </p>

            <p>
                Les utilisateurs disposent d’un droit d’accès, de rectification et de suppression
                de leurs données personnelles. Ces droits peuvent être exercés auprès du responsable
                du traitement en contactant l’adresse suivante :
                <a href="mailto:contactModif@ggvote.fr">contactModif@ggvote.fr</a>.
            </p>

            <p>
                Les responsables de l’application GGVote mettent en œuvre des mesures techniques
                et organisationnelles appropriées afin de garantir la sécurité des données à
                caractère personnel, notamment afin de :
            </p>

            <ul>
                <li>empêcher tout accès non autorisé aux données ;</li>
                <li>prévenir la perte, l’altération ou la divulgation des informations ;</li>
                <li>assurer la confidentialité des identifiants et des mots de passe.</li>
            </ul>
        </section>

        <section class="legal-section">
            <h2>Article 2 - Propriété intellectuelle</h2>
            <p>
                L’application GGVote, incluant notamment son code source, son architecture,
                sa base de données ainsi que l’ensemble de ses contenus textuels et graphiques,
                est protégée par le Code de la propriété intellectuelle.
            </p>

            <p>
                Toute reproduction, représentation, modification ou exploitation non autorisée
                de l’application ou de l’un de ses éléments constitue une contrefaçon et est
                susceptible d’entraîner des sanctions civiles et pénales conformément aux
                dispositions légales en vigueur.
            </p>

            <p>
                L’utilisation de l’application est strictement limitée à un usage personnel
                et non commercial dans le cadre du projet pédagogique. Toute utilisation non
                conforme aux présentes Conditions Générales d’Utilisation est susceptible
                d’engager la responsabilité de l’utilisateur.
            </p>

            <p>
                Les photographies et contenus visuels présents sur l’application GGVote sont
                utilisés exclusivement dans un cadre pédagogique. Ils ne font l’objet d’aucune
                exploitation commerciale et ne sont pas destinés à une diffusion publique en
                dehors du projet académique.
            </p>

            <p>
                Les concepteurs de l’application déclarent être conscients des règles applicables
                en matière de droits à l’image et de propriété intellectuelle et veillent à
                respecter les droits des tiers.
            </p>
        </section>

        <p class="legal-link-bottom">
            Les informations légales relatives à l’éditeur de l’application sont disponibles
            dans les
            <a href="mentions-legales.php">Mentions légales</a>.
        </p>

        <footer class="legal-footer">
            <p>Dernière mise à jour : <strong>07/01/2026</strong></p>
        </footer>

    </main>
</div>

<?php include('footer.php'); ?>
