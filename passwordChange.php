<?php

namespace music_matching_app;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use music_matching_app\lib\PDODatabase;
use music_matching_app\lib\TokenManager;
use music_matching_app\lib\PasswordChange;
use music_matching_app\lib\SessionManager;
use DateTime;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$ses = new SessionManager($db);

$loader = new \Twig\Loader\FilesystemLoader(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig\Environment($loader,[
    'cache' => Bootstrap::CACHE_DIR
]);
$res = [];

if(isset($_GET['password_reset_token'])){
    $token = $_GET['password_reset_token'];
    $token_type = 'password_reset';

    $tokenManager = new TokenManager($db);
    $accountInfo = $tokenManager->tokenExists($token, $token_type);

    $is_reset = $accountInfo[0]['is_reset'];
    $member_id = $accountInfo[0]['member_id'];
    $expiration_date = new DateTime($accountInfo[0]['expiration_date']);
    $res = $tokenManager->verifyToken($is_reset, $expiration_date);
    if($res['is_verify'] === false){
        $context = [];
        $context['title'] = Bootstrap::PASSWORD_RESET_TOKEN_FAILED_PAGE_TITLE;
        $context['sub_title'] =Bootstrap::PASSWORD_RESET_TOKEN_FAILED_PAGE_SUBTITLE;
        $context['text'] = Bootstrap::PASSWORD_RESET_TOKEN_FAILED_PAGE_TEXT;
        echo $twig->render('failed.html.twig', $context);
        exit();
    }

    if(isset($_POST['new_password'])){
        $newPassword = $_POST['new_password'];
        $change = new PasswordChange($db);
        $change->changePassword($newPassword, $member_id);
        echo 'パスワード変更完了';
    }
}else{
    header('Location: ' . Bootstrap::ENTRY_URL . 'eventlist.php');
    exit();
}

$context = [];
echo $twig->render('passwordChange.html.twig',[]);
