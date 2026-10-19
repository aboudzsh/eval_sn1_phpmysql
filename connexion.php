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
    if ($email === '' || $password === '') {
        $erreurs[] = 'Veuillez renseigner votre email et votre mot de passe.';
    } else {
        $requete = $dbh->prepare('SELECT * FROM user WHERE email = :email');
        $requete->execute(['email' => $email]);
        $utilisateur = $requete->fetch();
        if ($utilisateur && password_verify($password, $utilisateur['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $utilisateur['id'];
            $_SESSION['role'] = $utilisateur['role'];
            $_SESSION['flash'] = 'Vous êtes connecté.';
            header('Location: index.php');
            exit;
        }
        $erreurs[] = 'Email ou mot de passe incorrect.';
    }
}

$titrePage = 'Connexion';
require 'header.php';
?>
<div class="narrow">
    <div class="card">
        <h1>Connexion</h1>
        <?php require 'erreurs.php'; ?>
        <form class="form" method="post">
            <div>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= afficher($email) ?>" autocomplete="email" maxlength="180" required autofocus>
            </div>
            <div>
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" autocomplete="current-password" required>
            </div>
            <button type="submit">Se connecter</button>
        </form>
        <p class="form-footer">Pas encore de compte ? <a href="inscription.php">Créer un compte</a></p>
    </div>
</div>
<?php require 'footer.php'; ?>
