<?php
require 'config.php';
if (!$admin) {
    header('Location: index.php');
    exit;
}

$erreurs = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = valeur($_POST, 'id');
    if (!ctype_digit($id) || $id < 1) {
        $erreurs[] = 'Événement invalide.';
    } else {
        $requete = $dbh->prepare('UPDATE event SET is_validated = 1 WHERE id = :id AND is_validated = 0');
        $requete->execute(['id' => $id]);
        if ($requete->rowCount() === 1) {
            $_SESSION['flash'] = 'L’événement a été validé.';
            header('Location: validation.php');
            exit;
        }
        $erreurs[] = 'Cet événement est introuvable ou déjà validé.';
    }
}
$requete = $dbh->prepare('SELECT * FROM event WHERE is_validated = 0 ORDER BY date ASC');
$requete->execute();
$evenements = $requete->fetchAll();
$titrePage = 'Validation des événements';
require 'header.php';
?>
<div class="section-head">
    <div>
        <h1>Événements à valider</h1>
        <p class="muted"><?= afficher(count($evenements)) ?> événement(s) en attente de validation.</p>
    </div>
</div>
<?php require 'erreurs.php'; ?>
<?php if (count($evenements) === 0) { ?>
    <p class="empty">Aucun événement en attente.</p>
<?php } else { ?>
    <div class="card table-wrap">
        <table class="table">
            <thead><tr><th>Événement</th><th>Date</th><th>Catégorie</th><th>Places</th><th>Statut</th><th>Action</th></tr></thead>
            <tbody>
                <?php foreach ($evenements as $evenement) { ?>
                    <tr>
                        <td><a href="evenement.php?id=<?= afficher($evenement['id']) ?>"><?= afficher($evenement['title']) ?></a></td>
                        <td><?= afficher(dateLisible($evenement['date'])) ?></td>
                        <td><span class="badge cat-<?= afficher($evenement['category']) ?>"><?= afficher($categories[$evenement['category']]) ?></span></td>
                        <td><?= afficher($evenement['places']) ?></td>
                        <td><span class="badge badge-pending">Non validé</span></td>
                        <td><form method="post">
                            <input type="hidden" name="id" value="<?= afficher($evenement['id']) ?>">
                            <button type="submit" class="btn btn-small btn-success">Valider</button>
                        </form></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php } ?>
<?php require 'footer.php'; ?>
