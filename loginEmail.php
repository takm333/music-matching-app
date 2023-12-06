<?php

namespace music_matching_app;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use music_matching_app\lib\PDODatabase;
use music_matching_app\lib\Login;
use music_matching_app\lib\SessionManager;

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
if(isset($_POST['mail_address']) && isset($_POST['password'])){
    if($_POST['mail_address'] !== '' && isset($_POST['password']) !== ''){
        $login = new Login($db);
        $login->loginEmail($_POST['mail_address'], $_POST['password']);
    }
}

$context = [];
echo $twig->render('loginEmail.html.twig',[]);
