<?php

namespace music_matching_app;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use music_matching_app\lib\PDODatabase;
use music_matching_app\lib\SessionManager;
use music_matching_app\lib\Participation;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$ses = new SessionManager($db);

$loader = new \Twig\Loader\FilesystemLoader(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig\Environment($loader,[
    'cache' => Bootstrap::CACHE_DIR
]);
$ses->checkSession();
$participation = new Participation($db);
$json = file_get_contents('php://input');
$data = json_decode($json, true);
var_dump($data);

$member_id = isset($_SESSION['member_id']) ? $_SESSION['member_id'] : '';
$data = isset($_POST) ? $_POST: '';
$res = '';
if($data !== ''){
    $event_id = $data['event_id'];
    if(isset($data['regist']) && $data['regist'] === 'regist'){
        $status = $data['status'];
        $participation->insertParticipationStatus($member_id, $event_id, $status);
        $res = $participation->searchParticipationStatus($member_id, $event_id);
    }if(isset($data['cancel']) && $data['cancel'] === 'cancel'){
        $participation->cancelParticipation($member_id, $event_id);
        $res = $participation->searchParticipationStatus($member_id, $event_id);
    }
}
exit();
