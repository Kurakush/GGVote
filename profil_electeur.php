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
 *  État du vote (via les tokens)
 * 
 * vote(idvote, date_vote, heure_vote, idscrutin, idjoueur, idtoken)
 * token(idtoken, ..., idelecteur, ...)
 * ============================ */

$sqlVote = "SELECT 
                v.date_vote,
                v.heure_vote,
                j.pseudo,
                j.equipe,
                s.nom_scrutin,
                c.nom_compet
            FROM vote v
            JOIN token t       ON t.idtoken       = v.idtoken
            JOIN joueur j      ON j.idjoueur      = v.idjoueur
            JOIN scrutin s     ON s.idscrutin     = v.idscrutin
            JOIN competition c ON c.idcompetition = s.idcompetition
            WHERE t.idelecteur = :id
            ORDER BY v.date_vote DESC, v.heure_vote DESC
            LIMIT 1";

$stmtVote = $connexion->prepare($sqlVote);
$stmtVote->execute([':id' => $idelecteur]);
$vote = $stmtVote->fetch(PDO::FETCH_ASSOC);

$dejaVote = $vote !== false;


/* ============================
 *  Jetons de l'électeur
 *  token(idtoken, etat, date_generation, idadmin, idelecteur, code_token, token_hash, idcompetition)
 * ============================ */

$sqlTokens = "SELECT 
                t.code_token,
                t.etat,
                t.date_generation,
                c.nom_compet
             FROM token t
             JOIN competition c ON c.idcompetition = t.idcompetition
             WHERE t.idelecteur = :id
             ORDER BY c.nom_compet ASC, t.date_generation ASC";

$stmtTokens = $connexion->prepare($sqlTokens);
$stmtTokens->execute([':id' => $idelecteur]);
$tokens = $stmtTokens->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="profil-container">

    <h1 class="profil-title">Espace électeur</h1>

    <!-- INFOS PERSO -->
    <section class="profil-card">
        <h2 class="profil-card-title">Mes informations</h2>
        <p><strong>Email :</strong> <?= htmlspecialchars($email) ?></p>
        <p><strong>Type de membre :</strong> <?= htmlspecialchars($typeElecteur) ?></p>
    </section>

    <!-- MES JETONS DE VOTE -->
    <section class="profil-card">
        <h2 class="profil-card-title">Mes jetons de vote</h2>

        <?php if (empty($tokens)): ?>
            <p>Vous ne disposez actuellement d'aucun jeton de vote.</p>
            <p>Lorsque des compétitions seront ouvertes et que vous vous connecterez, des jetons vous seront attribués automatiquement.</p>
        <?php else: ?>
            <p>Voici les jetons associés à vos compétitions :</p>
            <table class="profil-table">
                <thead>
                    <tr>
                        <th>Compétition</th>
                        <th>Code du jeton</th>
                        <th>État</th>
                        <th>Date de génération</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tokens as $t): ?>
                        <tr>
                            <td><?= htmlspecialchars($t['nom_compet']) ?></td>
                            <td><code><?= htmlspecialchars($t['code_token']) ?></code></td>
                            <td>
                                <?php if ((int)$t['etat'] === 0): ?>
                                    <span class="token-status token-status-ok">Non utilisé</span>
                                <?php else: ?>
                                    <span class="token-status token-status-used">Utilisé</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($t['date_generation']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p class="profil-vote-note">
                Un jeton <strong>non utilisé</strong> vous permet de voter pour la compétition correspondante.
                Une fois le vote validé, le jeton passe en état <strong>utilisé</strong> et ne peut plus servir.
            </p>
        <?php endif; ?>
    </section>

    <!-- ÉTAT DU VOTE -->
    <section class="profil-card">
        <h2 class="profil-card-title">État de mon vote</h2>

        <?php if (!$dejaVote): ?>
            <p>Vous n'avez pas encore participé à un vote avec vos jetons.</p>
            <p>Rendez-vous sur la page <strong>Voter</strong> pour utiliser votre jeton et choisir votre MVP.</p>
        <?php else: ?>
            <p>Vous avez déjà effectué au moins un vote.</p>

            <div class="profil-vote-summary">
                <p><strong>Scrutin :</strong> <?= htmlspecialchars($vote['nom_scrutin']) ?></p>
                <p><strong>Compétition :</strong> <?= htmlspecialchars($vote['nom_compet']) ?></p>
                <p><strong>Candidat choisi :</strong> 
                    <?= htmlspecialchars($vote['pseudo']) ?> (<?= htmlspecialchars($vote['equipe']) ?>)
                </p>
                <p><strong>Date du vote :</strong> <?= htmlspecialchars($vote['date_vote']) ?> à <?= htmlspecialchars($vote['heure_vote']) ?></p>
                <p class="profil-vote-note">
                    Votre vote est <strong>définitif</strong> et ne peut plus être modifié.
                </p>
            </div>
        <?php endif; ?>
    </section>

    <!-- INFOS RÉGLEMENTAIRES -->
    <section class="profil-card">
        <h2 class="profil-card-title">Informations réglementaires</h2>
        <ul class="profil-rules-list">
            <li>Connexion obligatoire pour accéder à l’espace électeur.</li>
            <li>Un seul vote valide par compétition (un jeton utilisable une seule fois).</li>
            <li>Votre vote est enregistré de manière anonyme dans l’urne électronique.</li>
            <li>Après validation, il est impossible de modifier ou d’annuler votre vote.</li>
        </ul>
    </section>

</div>

<?php
require('footer.php');
?>
