<?php
require('header.php');

// Accès réservé aux électeurs connectés
if (!isset($_SESSION['electeur_email']) || !isset($_SESSION['idelecteur'])) {
    header("Location: index.php");
    exit;
}

$idelecteur = $_SESSION['idelecteur'];
$email      = $_SESSION['electeur_email'];

$connexion = dbconnect();
if (!$connexion) {
    die("Pb d'accès à la bdd");
}

/* ============================
 *  Infos de l'électeur
 * ============================ */
$sql = "SELECT email, type 
        FROM electeur 
        WHERE idelecteur = :id";
$stmt = $connexion->prepare($sql);
$stmt->execute([':id' => $idelecteur]);
$infos = $stmt->fetch(PDO::FETCH_ASSOC);

$typeElecteur = $infos['type'] ?? '—';

/* ============================
 *  Compétitions ouvertes (scrutin ouvert)
 * ============================ */
$sqlOpen = "SELECT DISTINCT c.idcompetition, c.nom_compet
            FROM scrutin s
            JOIN competition c ON c.idcompetition = s.idcompetition
            WHERE s.etat_scrutin = 'ouvert'
              AND s.date_ouverture <= NOW()
              AND s.date_cloture   >= NOW()
            ORDER BY c.nom_compet ASC";
$stmtOpen = $connexion->query($sqlOpen);
$competitionsOuvertes = $stmtOpen->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="profil-container">

    <h1 class="profil-title">Espace électeur</h1>

    <!-- MESSAGES FLASH -->
    <?php if (!empty($_SESSION['flash_message'])): ?>
        <div class="flash-success">
            <?= htmlspecialchars($_SESSION['flash_message']) ?>
        </div>
        <?php unset($_SESSION['flash_message']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="flash-error">
            <?= htmlspecialchars($_SESSION['flash_error']) ?>
        </div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <!-- BOUTON REGENERATION (si token bloqué) -->
    <?php if (!empty($_SESSION['regen_compet'])): ?>
        <div style="margin:12px 0;">
            <a class="btn-token" href="generer_token.php?idcompetition=<?= (int)$_SESSION['regen_compet'] ?>&force=1">
                Regénérer le token
            </a>
        </div>
    <?php endif; ?>

    <!-- INFOS PERSO -->
    <section class="profil-card">
        <h2 class="profil-card-title">Mes informations</h2>
        <p><strong>Email :</strong> <?= htmlspecialchars($email) ?></p>
        <p><strong>Type de membre :</strong> <?= htmlspecialchars($typeElecteur) ?></p>
    </section>

    <!-- TOKEN A COPIER (stocké en session, pas en base) -->
    <?php if (!empty($_SESSION['last_token']['code'])): ?>
        <section class="profil-card">
            <h2 class="profil-card-title">Mon token à copier</h2>

            <p>
                Voici votre dernier token généré. Copiez-le et utilisez-le sur la page de vote.
                <strong>Ne le partagez pas.</strong>
            </p>

            <div class="token-box">
                <code id="tokenToCopy"><?= htmlspecialchars($_SESSION['last_token']['code']) ?></code>
                <button type="button" class="btn-token" onclick="copyToken()">Copier</button>
            </div>

            <p class="profil-vote-note">
                Compétition : <?= htmlspecialchars((string)($_SESSION['last_token']['idcompetition'] ?? '')) ?> —
                Généré le : <?= htmlspecialchars((string)($_SESSION['last_token']['created_at'] ?? '')) ?>
            </p>

            <form method="post" action="token_clear.php" style="margin-top:10px;">
                <button type="submit" class="btn-token" style="background:#545454;">Masquer</button>
            </form>

            <script>
                function copyToken() {
                    const text = document.getElementById('tokenToCopy').innerText;
                    navigator.clipboard.writeText(text).then(() => {
                        alert("Token copié !");
                    });
                }
            </script>
        </section>
    <?php endif; ?>

    <!-- OBTENIR UN TOKEN -->
    <section class="profil-card">
        <h2 class="profil-card-title">Obtenir un token de vote</h2>

        <p>
            Un token est nécessaire pour participer à un vote.
            Afin de garantir l’anonymat, les tokens ne sont pas associés à votre identité en base de données.
            Le token est affiché via votre session : copiez-le et conservez-le.
        </p>

        <?php if (empty($competitionsOuvertes)): ?>
            <p>Aucune compétition n’est ouverte au vote pour le moment.</p>
        <?php else: ?>
            <table class="profil-table">
                <thead>
                    <tr>
                        <th>Compétition</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($competitionsOuvertes as $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['nom_compet']) ?></td>
                            <td>
                                <a class="btn-token"
                                   href="generer_token.php?idcompetition=<?= (int)$c['idcompetition'] ?>">
                                    Obtenir un token
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <p class="profil-vote-note">
                Une fois votre token généré, rendez-vous sur la page <strong>Voter</strong> pour l’utiliser.
            </p>
        <?php endif; ?>
    </section>

    <!-- ÉTAT DU VOTE -->
    <section class="profil-card">
        <h2 class="profil-card-title">État de mon vote</h2>
        <p>
            Pour garantir l’anonymat du vote, il n’est pas possible d’afficher un historique de vote associé à votre compte.
            Une confirmation s’affiche immédiatement après l’enregistrement du vote.
        </p>
    </section>

    <!-- INFOS RÉGLEMENTAIRES -->
    <section class="profil-card">
        <h2 class="profil-card-title">Informations réglementaires</h2>
        <ul class="profil-rules-list">
            <li>Connexion obligatoire pour accéder à l’espace électeur.</li>
            <li>Un token permet un seul vote et est invalide après utilisation.</li>
            <li>Le vote est enregistré de manière anonyme dans l’urne électronique.</li>
            <li>Une fois validé, le vote ne peut être ni modifié ni annulé.</li>
        </ul>
    </section>

</div>

<?php
require('footer.php');
?>
