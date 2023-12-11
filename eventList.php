<?php

namespace music_matching_app;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use music_matching_app\lib\PDODatabase;
use music_matching_app\lib\SessionManager;
use music_matching_app\lib\EventList;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$ses = new SessionManager($db);

$loader = new \Twig\Loader\FilesystemLoader(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig\Environment($loader,[
    'cache' => Bootstrap::CACHE_DIR
]);

$member_id = isset($_SESSION['member_id']) ? $_SESSION['member_id'] : '';
$event = new EventList($db, $member_id);
if(isset($_GET['event']) === false){
    $event->displayRecentEvent($member_id);
}else if($_GET['event'] === 'new'){
    $event->displayNewEvent($member_id);
}else if($_GET['event'] === 'my_favorite'){
    $event->displayFavoriteEvent($member_id);
}else{
    header('Location: ' . Bootstrap::ENTRY_URL . 'eventList.php');
    exit;
}
?>
<a href="http://localhost/music_matching_app/eventList.php">通常</a>
<a href="http://localhost/music_matching_app/eventList.php?event=new">新規</a>
<a href="http://localhost/music_matching_app/eventList.php?event=my_favorite">お気に入り</a>
