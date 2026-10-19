<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= afficher($titrePage) ?> · Agenda</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 128 128%22><text y=%221.2em%22 font-size=%2296%22>📅</text></svg>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container">
        <a class="logo" href="index.php">Agenda<span>.</span></a>
        <nav class="nav">
            <a href="index.php">Événements</a>
            <?php if ($admin) { ?>
                <a href="validation.php">Validation</a>
                <a href="inscrits.php">Inscrits</a>
            <?php } ?>
            <?php if ($connecte) { ?>
                <a class="btn btn-small" href="deconnexion.php">Se déconnecter</a>
            <?php } else { ?>
                <a href="connexion.php">Connexion</a>
                <a class="btn btn-small" href="inscription.php">Créer un compte</a>
            <?php } ?>
        </nav>
    </div>
</header>
<main class="container">
<?php if (isset($_SESSION['flash'])) { ?>
    <div class="flash flash-success" role="status"><?= afficher($_SESSION['flash']) ?></div>
    <?php unset($_SESSION['flash']); ?>
<?php } ?>
