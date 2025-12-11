<?php
// -------------------------------
//  CONTROLE D'ACCES AVANT HTML
// -------------------------------
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
 * Accès autorisé :
 *  - électeur connecté (idelecteur)
 *  - OU admin connecté (admin_id)
 */
if (!isset($_SESSION['idelecteur']) && !isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}
// A partir d'ici, on peut envoyer du HTML
require('header.php');

$connexion = dbconnect();
if (!$connexion) {
    die("Pb d'accès à la bdd");
}

$idjeu = 5; // CSGO
$now   = date('Y-m-d H:i:s');

/* --- Vérifier qu'il existe AU MOINS un scrutin ouvert pour LoL --- */
$sqlCheck = "SELECT COUNT(*)
             FROM scrutin s
             JOIN competition c ON c.idcompetition = s.idcompetition
             WHERE c.idjeu = :idjeu
               AND s.etat_scrutin = 'ouvert'
               AND s.date_ouverture <= :now
               AND s.date_cloture   >= :now";
$stmtCheck = $connexion->prepare($sqlCheck);
$stmtCheck->execute([':idjeu' => $idjeu, ':now' => $now]);

if ((int)$stmtCheck->fetchColumn() === 0) {
    ?>
    <div class="scrutin-info">
        <h2>Scrutin fermé</h2>
        <p>Aucun vote n'est actuellement ouvert pour CSGO:2.</p>
    </div>
    <?php
    require('footer.php');
    exit;
}

/* --- Compétitions CSGO qui ont un scrutin OUVERT --- */
$sql = "SELECT DISTINCT c.idcompetition, c.nom_compet, s.idscrutin
        FROM competition c
        JOIN scrutin s ON s.idcompetition = c.idcompetition
        WHERE c.idjeu = :idjeu
          AND s.etat_scrutin = 'ouvert'
          AND s.date_ouverture <= :now
          AND s.date_cloture   >= :now";
$stmt = $connexion->prepare($sql);
$stmt->execute([':idjeu' => $idjeu, ':now' => $now]);
$competitions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="about-us">
    <img src="images/csgo.jpg" alt="CSGO" width="300" height="200">
    <h1>MVP CSGO:2</h1>
    <p>Voter pour élire le MVP de CSGO:2 par compétitions !</p>
    <p><strong>Attention : votre vote ne sera plus modifiable après la validation.</strong></p>
</div>

<?php if (empty($competitions)): ?>
    <div class="scrutin-info">
        <p>Aucune compétition ouverte au vote pour le moment.</p>
    </div>
<?php endif; ?>

<?php foreach ($competitions as $comp): ?>

    <?php
    // Joueurs de cette compétition
    $sqlJ = "SELECT * FROM joueur
             WHERE idcompetition = :idcomp
             ORDER BY pseudo";
    $stmtJ = $connexion->prepare($sqlJ);
    $stmtJ->execute([':idcomp' => $comp['idcompetition']]);
    $joueurs = $stmtJ->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="compet">
        <h2><?= htmlspecialchars($comp['nom_compet']) ?></h2>
    </div>

    <?php if (count($joueurs) === 0): ?>

        <p style="text-align:center;margin-bottom:40px;">
            Aucun joueur enregistré pour cette compétition.
        </p>

    <?php else: ?>

        <?php if (isset($_SESSION['idelecteur'])): ?>

            <!-- FORMULAIRE DE VOTE POUR CETTE COMPÉTITION (ÉLECTEUR SEULEMENT) -->
            <form class="vote-form" action="vote_save.php" method="post">
                <input type="hidden" name="idscrutin" value="<?= (int)$comp['idscrutin'] ?>">

                <div class="sections-jeux">
                    <?php foreach ($joueurs as $j): ?>
                        <label class="tuiles tuiles-selectable">
                            <!-- Radio caché qui représente le choix -->
                            <input type="radio"
                                   name="idjoueur"
                                   value="<?= (int)$j['idjoueur'] ?>"
                                   required>

                            <div class="tuiles-content">
                                <?php if (!empty($j['photo'])): ?>
                                    <img src="images/<?= htmlspecialchars($j['photo']) ?>"
                                         alt="<?= htmlspecialchars($j['pseudo']) ?>">
                                <?php endif; ?>

                                <h3><?= strtoupper(htmlspecialchars($j['pseudo'])) ?></h3>
                                <p>Joueur chez <?= htmlspecialchars($j['equipe']) ?></p>

                                <a href="candidat_fiche.php?id=<?= (int)$j['idjoueur'] ?>" class="btn-fiche">
                                Voir la fiche
                                </a>

                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>

                <br><br>

                <div class="vote-token-field">
                    <label for="token_<?= (int)$comp['idscrutin'] ?>">Votre jeton de vote</label>
                    <input type="text"
                           id="token_<?= (int)$comp['idscrutin'] ?>"
                           name="token_code"
                           placeholder="Entrez votre jeton"
                           required>
                </div>

                <button type="submit" class="vote-btn">Valider mon vote</button>
            </form>

        <?php else: ?>

            <!-- ADMIN : CONSULTATION UNIQUEMENT -->
            <p style="text-align:center;margin-bottom:40px;color:#e31919;">
                Vous êtes connecté en tant qu'administrateur : consultation autorisée, vote désactivé.
            </p>

            <div class="sections-jeux">
                <?php foreach ($joueurs as $j): ?>
                    <div class="tuiles">
                        <div class="tuiles-content">
                            <?php if (!empty($j['photo'])): ?>
                                <img src="images/<?= htmlspecialchars($j['photo']) ?>"
                                     alt="<?= htmlspecialchars($j['pseudo']) ?>">
                            <?php endif; ?>

                            <h3><?= strtoupper(htmlspecialchars($j['pseudo'])) ?></h3>
                            <p>Joueur chez <?= htmlspecialchars($j['equipe']) ?></p>

                           
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>

    <?php endif; ?>

<?php endforeach; ?>

<?php require('footer.php'); ?>
