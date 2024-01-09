<?php

namespace music_matching_app;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use music_matching_app\lib\PDODatabase;
use music_matching_app\lib\SessionManager;
use music_matching_app\lib\Event;
use music_matching_app\lib\Favorite;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$ses = new SessionManager($db);

$loader = new \Twig\Loader\FilesystemLoader(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig\Environment($loader, [
    'cache' => Bootstrap::CACHE_DIR
]);
$ses->checkSession();

$member_id = isset($_SESSION['member_id']) ? $_SESSION['member_id'] : '';
$event_id = isset($_GET['event_id']) ? $_GET['event_id'] : '';

if($member_id !== '' && $event_id !== '') {
    $favorite = new Favorite($db);
    $is_favorite = $favorite->checkUserFavorite($member_id, $event_id);
    if($is_favorite === null) {
        $favorite->insertFavorite($member_id, $event_id);
    } else {
        $favorite->updateFavorite($member_id, $event_id, $is_favorite);
    }
    $favoriteObj = $favorite->createJson($member_id, $event_id);
    echo json_encode($favoriteObj);
} else {
    echo 'no';
}
exit();
