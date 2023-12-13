<?php

namespace music_matching_app;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use music_matching_app\lib\PDODatabase;
use music_matching_app\lib\SessionManager;
use music_matching_app\lib\EventList;
use music_matching_app\lib\initMaster;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$ses = new SessionManager($db);

$loader = new \Twig\Loader\FilesystemLoader(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig\Environment($loader,[
    'cache' => Bootstrap::CACHE_DIR
]);
$ses->checkSession();

$init = new initMaster($db);
[$genreArr, $areaArr] = $init->initEventList();
$res = [];

$member_id = isset($_SESSION['member_id']) ? $_SESSION['member_id'] : '';
$event = new EventList($db, $member_id);
$searchArr = (isset($_GET)) ? $_GET : [];
unset($searchArr['display']);
if(isset($_GET['display'])){
    $display = $_GET['display'];
    if($display === ''){
        $res = $event->displayRecentEvent($member_id, $searchArr);
    }else if($display === 'new'){
        $res = $event->displayNewEvent($member_id, $searchArr);
    }else if($display === 'my_favorite'){
        $res = $event->displayFavoriteEvent($member_id, $searchArr);
    }
}else{
    $res = $event->displayRecentEvent($member_id, $searchArr);
}
var_dump($res);
var_dump($display);

$context = [];
$context['searchArr'] = $searchArr;
$context['genreArr'] = $genreArr;
$context['areaArr'] = $areaArr;
$context['res'] = $res;
echo $twig->render('index.html.twig',$context);

?>
<html lang="en">
