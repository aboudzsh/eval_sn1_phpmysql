<?php
require 'config.php';

if ($connecte) {
    header('Location: index.php');
    exit;
}

$erreurs = [];
$email = trim(valeur($_POST, 'email'));
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = valeur($_POST, 'password');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 180) {
        $erreurs[] = 'Veuillez saisir un email valide de 180 caractères maximum.';
    }
    if (strlen($password) < 6) {
        $erreurs[] = 'Le mot de passe doit contenir au moins 6 caractères.';
    }
    if (strlen($password) > 72) {
        $erreurs[] = 'Le mot de passe est trop long.';
    }
    if (count($erreurs) === 0) {
        $requete = $dbh->prepare('SELECT id FROM user WHERE email = :email');
        $requete->execute(['email' => $email]);
        if ($requete->fetch()) {
            $erreurs[] = 'Cet email est déjà utilisé.';
        } else {
            try {
                $requete = $dbh->prepare("INSERT INTO user (email, password, role) VALUES (:email, :password, 'user')");
                $requete->execute(['email' => $email, 'password' => password_hash($password, PASSWORD_DEFAULT)]);
                $_SESSION['flash'] = 'Votre compte a été créé. Vous pouvez vous connecter.';
                header('Location: connexion.php');
                exit;
            } catch (PDOException $e) {
                if ($e->errorInfo[1] === 1062) {
                    $erreurs[] = 'Cet email est déjà utilisé.';
                } else {
                    $erreurs[] = 'Impossible de créer le compte pour le moment.';
                }
            }
        }
    }
}

$titrePage = 'Créer un compte';
require 'header.php';
?>
<div class="narrow">
    <div class="card">
        <h1>Créer un compte</h1>
        <?php require 'erreurs.php'; ?>
        <form class="form" method="post">
            <div>
                <label for="email" class="required">Email</label>
                <input type="email" id="email" name="email" value="<?= afficher($email) ?>" autocomplete="email" maxlength="180" required>
            </div>
            <div>
                <label for="password" class="required">Mot de passe</label>
                <input type="password" id="password" name="password" autocomplete="new-password" minlength="6" maxlength="72" required>
                <div class="help-text">6 caractères minimum.</div>
            </div>
            <button type="submit">Créer mon compte</button>
        </form>
        <p class="form-footer">Déjà inscrit ? <a href="connexion.php">Se connecter</a></p>
    </div>
</div>
<?php require 'footer.php'; ?>
