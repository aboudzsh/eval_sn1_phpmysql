<?php
require 'config.php';

$erreurs = [];
$titre = trim(valeur($_POST, 'title'));
$date = valeur($_POST, 'date');
$information = trim(valeur($_POST, 'information'));
$categorie = valeur($_POST, 'category');
$places = valeur($_POST, 'places');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $categorie = 'concert';
    $places = '20';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($titre === '' || strlen($titre) > 255) {
        $erreurs[] = 'Le titre est obligatoire et ne doit pas être trop long (255 caractères maximum).';
    }
    $dateValide = strtotime($date);
    if ($date === '' || $dateValide === false || date('Y-m-d\TH:i', $dateValide) !== $date || substr($date, 0, 4) < '1000') {
        $erreurs[] = 'Veuillez saisir une date et une heure valides.';
    }
    if ($information === '') {
        $erreurs[] = 'Les informations sont obligatoires.';
    }
    if (strlen($information) > 60000) {
        $erreurs[] = 'Les informations sont trop longues.';
    }
    if (!isset($categories[$categorie])) {
        $erreurs[] = 'Veuillez choisir une catégorie proposée.';
    }
    if (!ctype_digit($places) || $places < 1 || $places > 1000000) {
        $erreurs[] = 'Le nombre de places doit être un entier entre 1 et 1 000 000.';
    }
    if (count($erreurs) === 0) {
        $requete = $dbh->prepare('INSERT INTO event (title, date, information, category, places, image, is_validated) VALUES (:title, :date, :information, :category, :places, :image, 0)');
        $requete->execute([
            'title' => $titre,
            'date' => date('Y-m-d H:i:s', $dateValide),
            'information' => $information,
            'category' => $categorie,
            'places' => $places,
            'image' => $images[$categorie],
        ]);
        $_SESSION['flash'] = 'Votre événement a bien été ajouté. Il sera visible après validation par un administrateur.';
        header('Location: index.php');
        exit;
    }
}

$titrePage = 'Ajouter un événement';
require 'header.php';
?>
<div class="narrow">
    <a class="back-link" href="index.php">← Retour aux événements</a>
    <div class="card">
        <h1>Ajouter un événement</h1>
        <p class="muted">Votre événement sera visible après validation par un administrateur.</p>
        <?php require 'erreurs.php'; ?>
        <form class="form" method="post">
            <div>
                <label for="title" class="required">Titre</label>
                <input type="text" id="title" name="title" value="<?= afficher($titre) ?>" maxlength="255" placeholder="Ex. : Soirée jazz au Kiosque" required>
            </div>
            <div>
                <label for="date" class="required">Date</label>
                <input type="datetime-local" id="date" name="date" value="<?= afficher($date) ?>" required>
            </div>
            <div>
                <label for="information" class="required">Informations</label>
                <textarea id="information" name="information" placeholder="Lieu, programme, tarif..." required><?= afficher($information) ?></textarea>
            </div>
            <div>
                <label for="category">Catégorie</label>
                <select id="category" name="category">
                    <?php foreach ($categories as $cle => $nom) { ?>
                        <option value="<?= afficher($cle) ?>" <?php if ($categorie === $cle) { echo 'selected'; } ?>><?= afficher($nom) ?></option>
                    <?php } ?>
                </select>
            </div>
            <div>
                <label for="places">Nombre de places</label>
                <input type="number" id="places" name="places" min="1" max="1000000" value="<?= afficher($places) ?>" required>
            </div>
            <button type="submit">Enregistrer l'événement</button>
        </form>
    </div>
</div>
<?php require 'footer.php'; ?>
