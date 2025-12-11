<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'header.php';

$connexion = dbconnect();
if (!$connexion) {
    die("Pb d'accès à la bdd");
}

$idjoueur = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($idjoueur <= 0) {
    echo "<p style='text-align:center;margin:40px;'>Candidat introuvable.</p>";
    require 'footer.php';
    exit;
}

// On ne montre que les candidats VALIDÉS
$sql = "SELECT j.*, c.nom_compet
        FROM joueur j
        LEFT JOIN competition c ON c.idcompetition = j.idcompetition
        WHERE j.idjoueur = :id
          AND j.candidature_validee = 1";

$stmt = $connexion->prepare($sql);
$stmt->execute([':id' => $idjoueur]);
$cand = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cand) {
    echo "<p style='text-align:center;margin:40px;'>Candidat introuvable ou non validé.</p>";
    require 'footer.php';
    exit;
}
?>

<div class="profil-container">
    <h1 class="profil-title">Fiche du candidat</h1>

    <section class="profil-card">
        <div style="display:flex; gap:24px; align-items:flex-start; flex-wrap:wrap;">

            <?php if (!empty($cand['photo'])): ?>
                <div>
                    <img src="images/<?= htmlspecialchars($cand['photo']) ?>"
                         alt="<?= htmlspecialchars($cand['pseudo']) ?>"
                         style="max-width:240px;border-radius:8px;">
                </div>
            <?php endif; ?>

            <div>
                <h2><?= htmlspecialchars($cand['pseudo']) ?></h2>
                <p><strong>Équipe :</strong> <?= htmlspecialchars($cand['equipe'] ?? '—') ?></p>
                <p><strong>Poste :</strong> <?= htmlspecialchars($cand['poste'] ?? '—') ?></p>
                <p><strong>Âge :</strong> <?= $cand['age'] ? (int)$cand['age'] : '—' ?></p>
                <p><strong>Nationalité :</strong> <?= htmlspecialchars($cand['nationalite'] ?? '—') ?></p>
                <p><strong>Compétition :</strong> <?= htmlspecialchars($cand['nom_compet'] ?? '—') ?></p>
            </div>
        </div>
    </section>

    <section class="profil-card">
        <h2 class="profil-card-title">Bio / présentation</h2>
        <p><?= nl2br(htmlspecialchars($cand['bio_candidat'] ?? 'Aucune bio renseignée.')) ?></p>
    </section>

    <?php if (!empty($cand['lien_media'])): ?>
        <section class="profil-card">
            <h2 class="profil-card-title">Highlight / média</h2>
            <p>
                <a href="<?= htmlspecialchars($cand['lien_media']) ?>" target="_blank">
                    Voir le highlight du joueur
                </a>
            </p>
        </section>
    <?php endif; ?>
</div>

<?php require 'footer.php'; ?>
