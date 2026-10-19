<?php
require 'config.php';

$categorie = valeur($_GET, 'categorie');
if (!isset($categories[$categorie])) {
    $categorie = '';
}

$sql = 'SELECT * FROM event WHERE date > NOW() AND is_validated = 1';
$parametres = [];
if ($categorie !== '') {
    $sql .= ' AND category = :categorie';
    $parametres['categorie'] = $categorie;
}
$sql .= ' ORDER BY date ASC';
$requete = $dbh->prepare($sql);
$requete->execute($parametres);
$evenements = $requete->fetchAll();

$titrePage = 'Événements à venir';
require 'header.php';
?>
<section class="hero" style="background-image: url('images/rock.jpg')">
    <div class="hero-content">
        <span class="hero-kicker">Agenda local</span>
        <h1>Les événements près de chez vous</h1>
        <p>Concerts, conférences, ateliers et sport : trouvez votre prochaine sortie ou proposez la vôtre.</p>
        <a class="btn" href="ajouter.php">+ Ajouter un événement</a>
    </div>
</section>
<div class="section-head">
    <h2>À venir</h2>
    <ul class="legend">
        <li><a class="<?php if ($categorie === '') { echo 'active'; } ?>" href="index.php">Toutes</a></li>
        <?php foreach ($categories as $cle => $nom) { ?>
            <li class="cat-<?= afficher($cle) ?>"><a class="<?php if ($categorie === $cle) { echo 'active'; } ?>" href="index.php?categorie=<?= afficher($cle) ?>"><?= afficher($nom) ?></a></li>
        <?php } ?>
    </ul>
</div>
<?php if (count($evenements) === 0) { ?>
    <p class="empty">Aucun événement à venir pour le moment.</p>
<?php } else { ?>
    <ul class="event-list">
        <?php foreach ($evenements as $evenement) { ?>
            <li class="event-card cat-<?= afficher($evenement['category']) ?>">
                <div class="event-media">
                    <img class="event-image" src="<?= afficher(imageEvenement($evenement)) ?>" alt="">
                    <div class="event-date">
                        <span class="day"><?= afficher(date('d', strtotime($evenement['date']))) ?></span>
                        <span class="month"><?= afficher($mois[date('n', strtotime($evenement['date']))]) ?></span>
                    </div>
                    <span class="badge"><?= afficher($categories[$evenement['category']]) ?></span>
                </div>
                <h3 class="event-title"><a href="evenement.php?id=<?= afficher($evenement['id']) ?>"><?= afficher($evenement['title']) ?></a></h3>
                <span class="muted"><?= afficher(dateLisible($evenement['date'])) ?></span>
            </li>
        <?php } ?>
    </ul>
<?php } ?>
<?php require 'footer.php'; ?>
