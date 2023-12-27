<?php

namespace music_matching_app;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use music_matching_app\lib\PDODatabase;
use music_matching_app\lib\SessionManager;
use music_matching_app\lib\Event;
use music_matching_app\lib\initMaster;
use music_matching_app\lib\Favorite;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$ses = new SessionManager($db);

$loader = new \Twig\Loader\FilesystemLoader(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig\Environment($loader,[
    'cache' => Bootstrap::CACHE_DIR
]);
$ses->checkSession();

$init = new initMaster($db);
[$genreArr, $areaArr] = $init->initEventList();

$member_id = isset($_SESSION['member_id']) ? $_SESSION['member_id'] : '';
$is_login = ($member_id !== '') ? true : false;

$event = new Event($db);
$searchArr = (isset($_GET)) ? $_GET : [];
$searchGenreArr = [];
$res = [];
$display = (isset($searchArr['display'])) ? $searchArr['display'] : '';
unset($searchArr['display']);

if(isset($_GET['display'])){
    $display = $_GET['display'];
    if($display === ''){
        $res = $event->displayRecentEventList($member_id, $searchArr);
    }else if($display === 'new'){
        $res = $event->displayNewEventList($member_id, $searchArr);
    }else if($display === 'my_favorite'){
        $res = ($member_id !== '') ? $event->displayFavoriteEventList($member_id, $searchArr) : [];
    }else if($display === 'login'){
        header('Location: ' . Bootstrap::ENTRY_URL . 'login.php');
        exit();
    }else if($display === 'accountCreate'){
        header('Location: ' . Bootstrap::ENTRY_URL . 'accountCreate.php');
        exit();
    }
}else{
    $res = $event->displayRecentEventList($member_id, $searchArr);
}
if(isset($searchArr['Genre'])) $searchGenreArr = $searchArr['Genre'];
$context = [];
$context['searchArr'] = $searchArr;
$context['searchGenreArr'] = $searchGenreArr;
$context['genreArr'] = $genreArr;
$context['areaArr'] = $areaArr;
$context['display'] = $display;
$context['res'] = $res;
$context['is_login'] = $is_login;
echo $twig->render('eventList.html.twig',$context);
