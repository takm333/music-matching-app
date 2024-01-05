<?php

namespace music_matching_app;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use music_matching_app\lib\AccountRemove;
use music_matching_app\lib\PDODatabase;
use music_matching_app\lib\SessionManager;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$ses = new SessionManager($db);

$loader = new \Twig\Loader\FilesystemLoader(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig\Environment($loader,[
    'cache' => Bootstrap::CACHE_DIR
]);
$ses->checkSession();
if(!isset($_SESSION['member_id'])){
    header('Location: ' . Bootstrap::ENTRY_URL . 'login.php');
}

$member_id = isset($_SESSION['member_id']) ? $_SESSION['member_id'] : '';

if($member_id === ''){
    header('Location: ' . Bootstrap::ENTRY_URL . 'eventlist.php');
    exit();
}

if(isset($_POST['remove']) && $_POST['remove'] === 'remove'){
    $remove = new AccountRemove($db);
    $remove->removeUserAccount($member_id);
    $ses->destroy();
    $ses->deleteSession();
    $context = [];
    echo $twig->render('accountRemoveCompleted.html.twig',$context);
    exit();
}

$context = [];
echo $twig->render('accountRemove.html.twig',$context);
