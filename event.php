<?php

namespace music_matching_app;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use music_matching_app\lib\PDODatabase;
use music_matching_app\lib\SessionManager;
use music_matching_app\lib\Event;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$ses = new SessionManager($db);

$loader = new \Twig\Loader\FilesystemLoader(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig\Environment($loader,[
    'cache' => Bootstrap::CACHE_DIR
]);
$ses->checkSession();

$member_id = isset($_SESSION['member_id']) ? $_SESSION['member_id'] : '';
$is_login = ($member_id !== '') ? true : false;
if(isset($_GET['event_id']) && $_GET['event_id'] !== ''){
    $event_id = $_GET['event_id'];
}else{
    header('Location: ' . Bootstrap::ENTRY_URL . 'eventlist.php');
    exit();
}
$event = new Event($db);
$eventDetail = $event->displayDetailEvent($event_id, $member_id);

$context = [];
$context['eventDetail'] = $eventDetail;
$context['is_login'] = $is_login;
var_dump($context);
echo $twig->render('event.html.twig',$context);
