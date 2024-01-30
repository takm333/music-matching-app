<?php

namespace music_matching_app;

require_once dirname(__FILE__) . '/../Bootstrap.class.php';

use music_matching_app\lib\Csv;
use music_matching_app\lib\PDODatabase;
use music_matching_app\lib\Event;
use music_matching_app\lib\EventParticipants;
use music_matching_app\lib\Favorite;
use music_matching_app\lib\initMaster;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);

$loader = new \Twig\Loader\FilesystemLoader(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig\Environment($loader, [
    'cache' => Bootstrap::CACHE_DIR
]);

$member_id = 'admin';

$init = new initMaster($db);
[$genreArr, $areaArr] = $init->initEventList();
$tableHeader = $init->adminEventList();

$event = new Event($db, $member_id);
$page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page'] : 1;
unset($_GET['page']);

$searchArr = (isset($_GET)) ? $_GET : [];
$searchGenreArr = [];
$res = [];
$display = (isset($searchArr['display'])) ? $searchArr['display'] : '';
$csv = (isset($searchArr['csv'])) ? $searchArr['csv'] : '';

unset($searchArr['display']);
unset($searchArr['csv']);

$limit = Bootstrap::PAGE_LIMIT;
$offset = ($page - 1) * $limit;

$eventCounter = new Event($db, $member_id);
$eventCount = $eventCounter->countEvents($searchArr);
//csvエクスポート処理
if($csv === 'export') {
    $csvType = 'event_list';
    $csvHeader = ['イベントID', 'タイトル', '開始時刻', 'エリア', '場所'];
    $csvArr = [];

    $eventCsv = new Event($db, $member_id);
    if($display === '') {
        $res = $eventCsv->displayRecentEventList($member_id, $searchArr);
    } elseif($display === 'new') {
        $res = $eventCsv->displayNewEventList($member_id, $searchArr);
    }

    foreach($res as $value) {
        unset($value['image']);
        array_push($csvArr, $value);
    }

    $csv = new Csv($db);
    $csv->exportCsv($csvType, $csvHeader, $csvArr);

//csvダウンロード処理
}else if($csv === 'download'){
    $csvType = 'event_import_format';
    $csvHeader = ['タイトル', '画像', '開始時刻', '開演時刻', 'エリアID', '会場', 'リンク', '管理者ID', 'アーティスト名', 'ジャンルID', 'チケット名', '価格'];
    $csvArr = [];

    $csv = new Csv($db);
    $csv->exportCsv($csvType, $csvHeader, $csvArr);
}




//イベント情報取得
if(isset($_GET['display'])) {
    $display = $_GET['display'];
    if($display === '') {
        $res = $event->displayRecentEventList($member_id, $searchArr, $limit, $offset);
    } elseif($display === 'new') {
        $res = $event->displayNewEventList($member_id, $searchArr, $limit, $offset);
    }
} else {
    $res = $event->displayRecentEventList($member_id, $searchArr, $limit, $offset);
}

$adminInfoDb = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$eventParticipants = new EventParticipants($adminInfoDb);
$participantsArr = $eventParticipants->getNumberOfParticipants($res);

$favorites = new Favorite($adminInfoDb);
$favoritesArr = $favorites->getNumberOfFavorites($res);


//参加人数、お気に入り数をイベント情報の配列へ代入
$i = 0;
foreach($res as $val){
    $res[$i]['number_of_participants'] = $participantsArr[$i];
    $res[$i]['favorites'] = $favoritesArr[$i];
    $i++;
}

$maxPage = ceil($eventCount / $limit);

if(isset($searchArr['Genre'])) {
    $searchGenreArr = $searchArr['Genre'];
}
$context = [];
$context['searchArr'] = $searchArr;
$context['searchGenreArr'] = $searchGenreArr;
$context['genreArr'] = $genreArr;
$context['areaArr'] = $areaArr;
$context['tableHeader'] = $tableHeader;
$context['display'] = $display;
$context['res'] = $res;
$context['eventCount'] = $eventCount;
$context['page'] = $page;
$context['maxPage'] = $maxPage;
echo $twig->render('adminEventList.html.twig', $context);
