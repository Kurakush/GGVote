<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'header.php';


$connexion = dbconnect();
if (!$connexion) {
    die("Pb d'accès à la bdd");
}

// id du jeu Valorant
$idjeu = 1;

// 1) Récupérer les scrutins de ce jeu qui ont des RESULTATS
$sqlScrutins = "SELECT DISTINCT s.idscrutin, s.nom_scrutin, c.nom_compet
                FROM resultat r
                JOIN scrutin s   ON s.idscrutin = r.idscrutin
                JOIN competition c ON c.idcompetition = s.idcompetition
                WHERE c.idjeu = :idjeu
                ORDER BY c.nom_compet, s.nom_scrutin";
$stmtScrutins = $connexion->prepare($sqlScrutins);
$stmtScrutins->execute([':idjeu' => $idjeu]);
$scrutins = $stmtScrutins->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="about-us">
    <img src="images/valorant.jpg" alt="Valorant" width="200" height="200">
    <h1>Résultats - Valorant</h1>
    <p>Découvrez les résultats des scrutins clôturés pour Valorant.</p>
</div>

<?php if (empty($scrutins)): ?>

    <div class="scrutin-info">
        <h2>Aucun résultat disponible</h2>
        <p>Aucun scrutin Valorant clôturé n’a encore été traité.</p>
    </div>

<?php else: ?>

    <?php foreach ($scrutins as $sc): ?>

        <?php
        // 2) Résultats détaillés pour ce scrutin
        $sqlRes = "SELECT r.nb_votes, r.rang,
                          j.pseudo, j.equipe
                   FROM resultat r
                   JOIN joueur j ON j.idjoueur = r.idjoueur
                   WHERE r.idscrutin = :idscrutin
                   ORDER BY r.rang ASC";
        $stmtRes = $connexion->prepare($sqlRes);
        $stmtRes->execute([':idscrutin' => (int)$sc['idscrutin']]);
        $rows = $stmtRes->fetchAll(PDO::FETCH_ASSOC);

        // calcul du total de votes pour les pourcentages
        $totalVotes = array_sum(array_column($rows, 'nb_votes'));
        ?>

        <div class="compet">
            <h2><?= htmlspecialchars($sc['nom_compet']) ?> - 
                <span style="font-size:0.8em;"><?= htmlspecialchars($sc['nom_scrutin']) ?></span>
            </h2>
        </div>

        <?php if ($totalVotes == 0): ?>
            <p style="text-align:center;margin-bottom:40px;">
                Aucun vote enregistré pour ce scrutin.
            </p>
        <?php else: ?>

            <table class="table-resultats">
                <thead>
                    <tr>
                        <th>Rang</th>
                        <th>Joueur</th>
                        <th>Équipe</th>
                        <th>Votes</th>
                        <th>%</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $row): 
                    $pourcent = $totalVotes > 0
                        ? round(($row['nb_votes'] / $totalVotes) * 100, 1)
                        : 0;
                ?>
                    <tr>
                        <td>#<?= (int)$row['rang'] ?></td>
                        <td><?= htmlspecialchars($row['pseudo']) ?></td>
                        <td><?= htmlspecialchars($row['equipe'] ?? '') ?></td>
                        <td><?= (int)$row['nb_votes'] ?></td>
                        <td><?= $pourcent ?>%</td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <p style="text-align:right;margin-bottom:40px;">
                Total des votes : <strong><?= $totalVotes ?></strong>
            </p>

        <?php endif; ?>

    <?php endforeach; ?>

<?php endif; ?>

<?php require 'footer.php'; ?>
