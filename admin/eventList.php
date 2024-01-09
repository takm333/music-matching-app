<?php

namespace music_matching_app;

require_once dirname(__FILE__) . '/../Bootstrap.class.php';

use music_matching_app\lib\CsvExport;
use music_matching_app\lib\PDODatabase;
use music_matching_app\lib\SessionManager;
use music_matching_app\lib\Event;
use music_matching_app\lib\initMaster;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$ses = new SessionManager($db);

$loader = new \Twig\Loader\FilesystemLoader(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig\Environment($loader,[
    'cache' => Bootstrap::CACHE_DIR
]);

$init = new initMaster($db);
[$genreArr, $areaArr] = $init->initEventList();

$event = new Event($db);
$page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page'] : 1;
unset($_GET['page']);

$searchArr = (isset($_GET)) ? $_GET : [];
$searchGenreArr = [];
$res = [];
$display = (isset($searchArr['display'])) ? $searchArr['display'] : '';
$csv = (isset($searchArr['csv'])) ? $searchArr['csv'] : '';

unset($searchArr['display']);
unset($searchArr['csv']);



//管理者はmember_idを持たないため、空文字
$admin = '';

$limit = Bootstrap::PAGE_LIMIT;
$offset = ($page - 1) * $limit;

$eventCounter = new Event($db);
$eventCount = $eventCounter->countEvents($searchArr);

if($csv === 'export'){
    $csvType = 'eventList';
    $csvHeader = ['イベントID', 'タイトル', '開始時刻', 'エリア', '場所'];
    $csvArr = [];

    $eventCsv = new Event($db);
    if($display === ''){
        $res = $eventCsv->displayRecentEventList($admin, $searchArr);
    }else if($display === 'new'){
        $res = $eventCsv->displayNewEventList($admin, $searchArr);
    }

    foreach($res as $value){
        unset($value['image']);
        array_push($csvArr, $value);
    }

    $csv = new CsvExport();
    $csv->exportCsv($csvType, $csvHeader, $csvArr);
}

if(isset($_GET['display'])){
    $display = $_GET['display'];
    if($display === ''){
        $res = $event->displayRecentEventList($admin, $searchArr, $limit, $offset);
    }else if($display === 'new'){
        $res = $event->displayNewEventList($admin, $searchArr, $limit, $offset);
    }
}else{
    $res = $event->displayRecentEventList($admin, $searchArr, $limit, $offset);
}

$maxPage = ceil($eventCount / $limit);

if(isset($searchArr['Genre'])) $searchGenreArr = $searchArr['Genre'];
$context = [];
$context['searchArr'] = $searchArr;
$context['searchGenreArr'] = $searchGenreArr;
$context['genreArr'] = $genreArr;
$context['areaArr'] = $areaArr;
$context['display'] = $display;
$context['res'] = $res;
$context['eventCount'] = $eventCount;
$context['page'] = $page;
$context['maxPage'] = $maxPage;
echo $twig->render('adminEventList.html.twig',$context);
