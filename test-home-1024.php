<!DOCTYPE html>
<html lang="tr" data-gender="female" data-device="embedded" data-view="home">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=1024, initial-scale=1">
    <title>Home Test — CoreMusic</title>
    <link rel="stylesheet" href="assets.coremusic.net/Css/main.css">
    <style>
        html, body { margin: 0; padding: 0; overflow: hidden; height: 100vh; }
        body { background: #1a1020; }
    </style>
</head>
<body>
<?php
session_start();
$_SESSION['MM_Username'] = 'Bayram Ali';
$_SESSION['MM_Email'] = 'bayram@coremusic.net';
$_SESSION['cm_gender'] = 'female';
$_SESSION['current_song'] = 'Sevil Neşelen';
$_SESSION['current_album'] = 'Hayat Rüya Gibi';
$_SESSION['current_artist'] = 'Göksel';
$_SESSION['current_art'] = 'assets.coremusic.net/Image/res-pink/default-album.png';
$_SESSION['volume'] = 100;

require __DIR__ . '/home.coremusic.net/header.php';
require __DIR__ . '/home.coremusic.net/pages/home.php';
require __DIR__ . '/home.coremusic.net/footer.php';
?>
</body>
</html>
