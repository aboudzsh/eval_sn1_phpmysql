<?php
session_start();
date_default_timezone_set('Europe/Paris');

try {
    $dbh = new PDO('mysql:host=127.0.0.1;port=8889;dbname=agenda;charset=utf8mb4', 'root', 'root', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $requeteFuseau = $dbh->prepare('SET time_zone = :fuseau');
    $requeteFuseau->execute(['fuseau' => date('P')]);
} catch (PDOException $e) {
    http_response_code(503);
    exit('Connexion impossible. Vérifiez que MySQL est démarré dans MAMP, que la base agenda est importée et que le port de config.php est correct.');
}

$categories = [
    'concert' => 'Concert',
    'conference' => 'Conférence',
    'atelier' => 'Atelier',
    'sport' => 'Sport',
];
$images = [
    'concert' => 'images/concert.jpg',
    'conference' => 'images/conference.jpg',
    'atelier' => 'images/atelier.jpg',
    'sport' => 'images/sport.jpg',
];
$mois = ['', 'janv.', 'févr.', 'mars', 'avr.', 'mai', 'juin', 'juil.', 'août', 'sept.', 'oct.', 'nov.', 'déc.'];

function valeur($tableau, $cle)
{
    if (isset($tableau[$cle]) && is_string($tableau[$cle])) {
        return $tableau[$cle];
    }
    return '';
}

function afficher($texte)
{
    return htmlspecialchars((string) $texte, ENT_QUOTES, 'UTF-8');
}

function dateLisible($date)
{
    return date('d/m/Y H\hi', strtotime($date));
}

function imageEvenement($evenement)
{
    $imagesAutorisees = ['images/concert.jpg', 'images/conference.jpg', 'images/atelier.jpg', 'images/sport.jpg', 'images/rock.jpg', 'images/escalade.jpg'];
    if (in_array($evenement['image'], $imagesAutorisees, true)) {
        return $evenement['image'];
    }
    return 'images/atelier.jpg';
}

$connecte = isset($_SESSION['user_id']);
$admin = $connecte && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
