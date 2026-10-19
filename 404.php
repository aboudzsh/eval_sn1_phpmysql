<?php
require_once 'config.php';
http_response_code(404);
$titrePage = 'Page introuvable';
require 'header.php';
?>
<div class="error-page">
    <p class="code">404</p>
    <h1>Cet événement n'existe pas</h1>
    <p class="muted">La page que vous cherchez est introuvable ou cet événement n'est pas encore publié.</p>
    <a class="btn" href="index.php">Retour aux événements</a>
</div>
<?php require 'footer.php'; ?>
