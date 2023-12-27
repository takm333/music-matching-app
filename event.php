<?php

namespace music_matching_app;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use music_matching_app\lib\PDODatabase;
use music_matching_app\lib\SessionManager;
use music_matching_app\lib\Event;
use music_matching_app\lib\EventParticipants;
use music_matching_app\lib\Favorite;
use music_matching_app\lib\initMaster;
use music_matching_app\lib\Participation;

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

$favorite = new Favorite($db);
$favorites = $favorite->countFavorite($event_id);
$is_favorite = $favorite->checkUserFavorite($member_id, $event_id);

//画面表示用
$init = new initMaster($db);
$statusArr = $init->initParticipantStatusList();

//自分の参加ステータス
$participation = new Participation($db);
$participationStatus = $participation->searchParticipationStatus($member_id, $event_id);

// 他の参加者
// 参加中のみ見える
$participantsArr = [];
// if($participationStatus !== 99 && $participationStatus !== ''){
    $participantsDb = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
    $participants = new EventParticipants($participantsDb);
    $participantsArr = $participants->searchEventParticipants($member_id, $event_id);
// }

//イベント情報取得
$eventDb = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$event = new Event($eventDb);
$eventDetail = $event->displayDetailEvent($event_id, $member_id);

//他のイベント情報取得
$eventListDb = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$eventList = new Event($eventDb);
$eventList = $eventList->displayNewEventList($member_id, []);

$context = [];
$context['eventDetail'] = $eventDetail;
$context['is_login'] = $is_login;
$context['favorites'] = $favorites;
$context['is_favorite'] = $is_favorite;
$context['statusArr'] = $statusArr;
$context['participationStatus'] = $participationStatus;
$context['eventList'] = $eventList;
$context['participantsArr'] = $participantsArr;

echo $twig->render('event.html.twig',$context);
