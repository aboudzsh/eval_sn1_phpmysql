<?php
require 'config.php';
if (!$admin) {
    header('Location: index.php');
    exit;
}

$requete = $dbh->prepare('SELECT * FROM event WHERE is_validated = 1 ORDER BY date ASC');
$requete->execute();
$evenements = $requete->fetchAll();
$requeteInscrits = $dbh->prepare('SELECT user.email, reservation.created_at FROM reservation INNER JOIN user ON user.id = reservation.user_id WHERE reservation.event_id = :event_id ORDER BY reservation.created_at ASC, reservation.id ASC');

$titrePage = 'Inscrits aux événements';
require 'header.php';
?>
<div class="section-head">
    <div>
        <h1>Inscrits aux événements</h1>
        <p class="muted">Liste des participants pour chaque événement validé.</p>
    </div>
</div>
<?php if (count($evenements) === 0) { ?>
    <p class="empty">Aucun événement validé pour le moment.</p>
<?php } ?>
<?php foreach ($evenements as $evenement) { ?>
    <?php
    $requeteInscrits->execute(['event_id' => $evenement['id']]);
    $inscrits = $requeteInscrits->fetchAll();
    ?>
    <section class="card attendees">
        <div class="attendees-head">
            <div>
                <h2><a href="evenement.php?id=<?= afficher($evenement['id']) ?>"><?= afficher($evenement['title']) ?></a></h2>
                <span class="badge cat-<?= afficher($evenement['category']) ?>"><?= afficher($categories[$evenement['category']]) ?></span>
                <span class="muted"><?= afficher(dateLisible($evenement['date'])) ?></span>
            </div>
            <p class="places">
                <?php if (count($inscrits) >= $evenement['places']) { ?>
                    <span class="badge badge-full">Complet</span>
                <?php } ?>
                <strong><?= afficher(count($inscrits)) ?></strong> inscrit(s) sur <?= afficher($evenement['places']) ?> places
            </p>
        </div>
        <?php if (count($inscrits) === 0) { ?>
            <p class="empty">Aucun inscrit pour le moment.</p>
        <?php } else { ?>
            <div class="table-wrap">
                <table class="table">
                    <thead><tr><th>Email</th><th>Inscrit le</th></tr></thead>
                    <tbody>
                        <?php foreach ($inscrits as $inscrit) { ?>
                            <tr><td><?= afficher($inscrit['email']) ?></td><td><?= afficher(dateLisible($inscrit['created_at'])) ?></td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>
    </section>
<?php } ?>
<?php require 'footer.php'; ?>
