<?php
require 'config.php';

$id = valeur($_GET, 'id');
if (!ctype_digit($id) || $id < 1) {
    require '404.php';
    exit;
}
$requete = $dbh->prepare('SELECT *, date > NOW() AS a_venir FROM event WHERE id = :id');
$requete->execute(['id' => $id]);
$evenement = $requete->fetch();
if (!$evenement || (!$evenement['is_validated'] && !$admin)) {
    require '404.php';
    exit;
}

$requete = $dbh->prepare('SELECT COUNT(*) AS total FROM reservation WHERE event_id = :event_id');
$requete->execute(['event_id' => $id]);
$nombreInscrits = $requete->fetch()['total'];
$placesRestantes = $evenement['places'] - $nombreInscrits;
if ($placesRestantes < 0) {
    $placesRestantes = 0;
}

$inscrit = false;
if ($connecte) {
    $requete = $dbh->prepare('SELECT id FROM reservation WHERE event_id = :event_id AND user_id = :user_id');
    $requete->execute(['event_id' => $id, 'user_id' => $_SESSION['user_id']]);
    $inscrit = $requete->fetch();
}

$erreurs = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$connecte) {
        header('Location: connexion.php');
        exit;
    }
    $action = valeur($_POST, 'action');
    if ($action === 'desinscrire' && $inscrit) {
        $requete = $dbh->prepare('DELETE FROM reservation WHERE user_id = :user_id AND event_id = :event_id');
        $requete->execute(['user_id' => $_SESSION['user_id'], 'event_id' => $id]);
        $_SESSION['flash'] = 'Vous êtes désinscrit de cet événement.';
        header('Location: evenement.php?id=' . $id);
        exit;
    } elseif ($action === 'inscrire') {
        if (!$evenement['is_validated'] || !$evenement['a_venir']) {
            $erreurs[] = 'Les inscriptions sont fermées pour cet événement.';
        } elseif ($inscrit) {
            $erreurs[] = 'Vous êtes déjà inscrit à cet événement.';
        } elseif ($placesRestantes === 0) {
            $erreurs[] = 'Cet événement est complet.';
        } else {
            try {
                $requete = $dbh->prepare('INSERT INTO reservation (user_id, event_id) VALUES (:user_id, :event_id)');
                $requete->execute(['user_id' => $_SESSION['user_id'], 'event_id' => $id]);
                $_SESSION['flash'] = 'Votre inscription à cet événement est confirmée.';
                header('Location: evenement.php?id=' . $id);
                exit;
            } catch (PDOException $e) {
                if ($e->errorInfo[1] === 1062) {
                    $erreurs[] = 'Vous êtes déjà inscrit à cet événement.';
                } else {
                    $erreurs[] = 'Impossible de vous inscrire pour le moment.';
                }
            }
        }
    } else {
        $erreurs[] = 'Action impossible.';
    }
}

$pourcentage = 0;
if ($evenement['places'] > 0) {
    $pourcentage = round(($evenement['places'] - $placesRestantes) / $evenement['places'] * 100);
}
$titrePage = $evenement['title'];
require 'header.php';
?>
<a class="back-link" href="index.php">← Retour aux événements</a>
<?php require 'erreurs.php'; ?>
<article class="cat-<?= afficher($evenement['category']) ?>">
    <header class="detail-hero" style="background-image: url('<?= afficher(imageEvenement($evenement)) ?>')">
        <div class="detail-hero-content">
            <span class="badge"><?= afficher($categories[$evenement['category']]) ?></span>
            <?php if (!$evenement['is_validated']) { ?>
                <span class="badge badge-pending">Non validé</span>
            <?php } ?>
            <h1><?= afficher($evenement['title']) ?></h1>
            <p><?= afficher(dateLisible($evenement['date'])) ?></p>
        </div>
    </header>
    <div class="detail-grid">
        <section class="card">
            <h2>Informations</h2>
            <p><?= nl2br(afficher($evenement['information'])) ?></p>
        </section>
        <aside class="card booking">
            <p class="places"><strong><?= afficher($placesRestantes) ?></strong> places restantes sur <?= afficher($evenement['places']) ?></p>
            <div class="progress"><span style="width: <?= afficher($pourcentage) ?>%"></span></div>
            <?php if ($placesRestantes === 0) { ?>
                <p><span class="badge badge-full">Complet</span></p>
            <?php } ?>
            <?php if ($inscrit) { ?>
                <p class="muted">Vous êtes inscrit à cet événement.</p>
                <form method="post">
                    <button type="submit" name="action" value="desinscrire" class="btn btn-secondary btn-block">Se désinscrire</button>
                </form>
            <?php } elseif (!$evenement['is_validated'] || !$evenement['a_venir']) { ?>
                <p class="muted">Les inscriptions sont fermées pour cet événement.</p>
            <?php } elseif ($placesRestantes === 0) { ?>
                <button class="btn btn-block" disabled>Complet</button>
            <?php } elseif (!$connecte) { ?>
                <a class="btn btn-block" href="connexion.php">Connectez-vous pour vous inscrire</a>
            <?php } else { ?>
                <form method="post">
                    <button type="submit" name="action" value="inscrire" class="btn btn-block">S'inscrire</button>
                </form>
            <?php } ?>
        </aside>
    </div>
</article>
<?php require 'footer.php'; ?>
