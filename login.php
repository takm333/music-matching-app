<?php

namespace music_matching_app;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use music_matching_app\lib\PDODatabase;
use music_matching_app\lib\Login;
use music_matching_app\lib\SessionManager;
use music_matching_app\master\initMaster;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$ses = new SessionManager($db);

$loader = new \Twig\Loader\FilesystemLoader(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig\Environment($loader,[
    'cache' => Bootstrap::CACHE_DIR
]);
$ses->checkSession();
if(isset($_SESSION['member_id'])){
    header('Location: ' . Bootstrap::ENTRY_URL . 'eventlist.php');
}

$context = [];
echo $twig->render('login.html.twig',$context);
