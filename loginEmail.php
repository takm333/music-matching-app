<?php

namespace music_matching_app;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use music_matching_app\lib\PDODatabase;
use music_matching_app\lib\Login;
use music_matching_app\lib\SessionManager;
use music_matching_app\lib\UserValidator;
use music_matching_app\lib\Recaptcha;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$ses = new SessionManager($db);
$dataArr = [];
$errArr = [];

$loader = new \Twig\Loader\FilesystemLoader(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig\Environment($loader, [
    'cache' => Bootstrap::CACHE_DIR
]);
$ses->checkSession();

if(isset($_SESSION['member_id'])) {
    header('Location: ' . Bootstrap::ENTRY_URL . 'eventlist.php');
}

if(isset($_POST['mail_address']) && isset($_POST['password'])) {

    $recaptcha = new Recaptcha();
    $res = $recaptcha->checkBot();

    if($res !== true) {
        $context['title'] = Bootstrap::REGIST_FAILED_PAGE_TITLE;
        $context['sub_title'] = Bootstrap::REGIST_FAILED_PAGE_SUBTITLE;
        $context['text'] = Bootstrap::REGIST_FAILED_PAGE_TEXT;
        echo $twig->render('failed.html.twig', $context);
        exit();
    }

    $dataArr = $_POST;

    $validator = new UserValidator($db);
    $errArr = $validator->checkError($dataArr);
    $errFlg = $validator->getErrFlg();
    if($errFlg) {
        $login = new Login($db);
        $errArr['mail_address'] = $login->loginEmail($_POST['mail_address'], $_POST['password']);
        $errArr['password'] = ' ';
    }
}

$context = [];
$context['dataArr'] = $dataArr;
$context['errArr'] = $errArr;
echo $twig->render('loginEmail.html.twig', $context);
