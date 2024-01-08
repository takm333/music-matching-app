<?php

namespace music_matching_app;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use music_matching_app\lib\PDODatabase;
use music_matching_app\lib\AccountCreateEmail;
use music_matching_app\lib\SessionManager;
use music_matching_app\lib\UserValidator;
use music_matching_app\lib\Recaptcha;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$ses = new SessionManager($db);
$account = new AccountCreateEmail($db);
$dataArr = [];
$errArr = [];

$loader = new \Twig\Loader\FilesystemLoader(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig\Environment($loader,[
    'cache' => Bootstrap::CACHE_DIR
]);

$ses->checkSession();
if(isset($_SESSION['member_id'])){
    header('Location: ' . Bootstrap::ENTRY_URL . 'eventlist.php');
    exit();
}

if(isset($_POST['mail_address']) && isset($_POST['password'])){
    $recaptcha = new Recaptcha();
    $res = $recaptcha->checkBot();

    if($res !== true){
        $context['title'] = Bootstrap::REGIST_FAILED_PAGE_TITLE;
        $context['sub_title'] =Bootstrap::REGIST_FAILED_PAGE_SUBTITLE;
        $context['text'] = Bootstrap::REGIST_FAILED_PAGE_TEXT;
        echo $twig->render('failed.html.twig', $context);
        exit();
    }

    $dataArr = $_POST;

    $validator = new UserValidator($db);
    $validator->accountExists($dataArr);
    $errArr = $validator->checkError($dataArr);
    $errFlg = $validator->getErrFlg();
    if($errFlg){
        $res = $account->registPreAccount($dataArr['mail_address'], $dataArr['password']);
        if($res){
            $context = [];
            $context['title'] = Bootstrap::REGIST_PAGE_TITLE;
            $context['sub_title'] =Bootstrap::REGIST_PAGE_SUBTITLE;
            $context['text'] = Bootstrap::REGIST_PAGE_TEXT;
            echo $twig->render('sendMail.html.twig', $context);
            exit();
        }else{
            $context['title'] = Bootstrap::REGIST_FAILED_PAGE_TITLE;
            $context['sub_title'] =Bootstrap::REGIST_FAILED_PAGE_SUBTITLE;
            $context['text'] = Bootstrap::REGIST_FAILED_PAGE_TEXT;
            echo $twig->render('failed.html.twig', $context);
            exit();
        }
    }
}
$context = [];
$context['dataArr'] = $dataArr;
$context['errArr'] = $errArr;
echo $twig->render('accountCreateEmail.html.twig',$context);
