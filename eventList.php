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
$ses->checkSession();

$member_id = isset($_SESSION['member_id']) ? $_SESSION['member_id'] : '';
$event = new EventList($db, $member_id);
$searchArr = (isset($_GET)) ? $_GET : [];
unset($searchArr['event']);
if($_GET['event'] === ""){
    $event->displayRecentEvent($member_id, $searchArr);
}else if($_GET['event'] === 'new'){
    $event->displayNewEvent($member_id, $searchArr);
}else if($_GET['event'] === 'my_favorite'){
    $event->displayFavoriteEvent($member_id, $searchArr);
}else{
    echo 'a';
}

var_dump($_GET);

?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="">
        <input type="text" name="SearchBox" placeholder="イベント名、アーティスト名、会場名で検索ができます。">
        <select name="Area">
            <option value="">地域</option>
            <option value="1">北海道</option>
            <option value="13">東京</option>
            <option value="23">愛知</option>
            <option value="27">大阪</option>
        </select>
        <div>
            <label for=""><input type="checkbox" name="Genre[]" value="0">全て</label>
            <label for=""><input type="checkbox" name="Genre[]" value="1">jpop</label>
            <label for=""><input type="checkbox" name="Genre[]" value="6">anime</label>
        </div>
        <input type="date" name="Between[]">
        <input type="date" name="Between[]">

        <button type="submit" name="event" value="">通常</button>
        <button type="submit" name="event" value="new">新規</button>
        <button type="submit" name="event" value="my_favorite">お気に入り</button>
    </form>


</body>
</html>
