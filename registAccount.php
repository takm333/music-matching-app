<?php

namespace music_matching_app;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use music_matching_app\lib\PDODatabase;
use music_matching_app\lib\SessionManager;
use music_matching_app\lib\TokenManager;
use music_matching_app\lib\AccountCreateEmail;
use music_matching_app\lib\Login;
use DateTime;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$ses = new SessionManager($db);

$loader = new \Twig\Loader\FilesystemLoader(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig\Environment($loader, [
    'cache' => Bootstrap::CACHE_DIR
]);

if(isset($_GET['url_token'])) {
    $token = $_GET['url_token'];
    $token_type = 'account_regist';

    $tokenManager = new TokenManager($db);
    $accountInfo = $tokenManager->tokenExists($token, $token_type);
    if(count($accountInfo) !== 0) {
        $is_registered = $accountInfo[0]['is_registered'];
        $expiration_date = new DateTime($accountInfo[0]['expiration_date']);
        $res = $tokenManager->verifyToken($is_registered, $expiration_date);
        if($res['is_verify']) {
            $accountCreate = new AccountCreateEmail($db);
            $member_id = $accountCreate->registAccount($accountInfo[0]);
            $login = new Login($db);
            $login->firstLoginEmail($accountInfo[0]['mail_address'], $accountInfo[0]['password_hash']);
            exit();
        } else {
            $errMsg = $res['error'];
            $type = $res['type'];
        }
    } else {
        $errMsg = '不正なリクエストです。';
        $type = 'login';
    }
} else {
    $errMsg = '不正なリクエストです。';
    $type = 'login';
}

$context = [];
$context['errMsg'] = $errMsg;
$context['type'] = $type;
echo $twig->render('error.html.twig', $context);
