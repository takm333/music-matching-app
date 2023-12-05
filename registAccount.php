<?php

namespace music_matching_app;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use music_matching_app\lib\PDODatabase;
use music_matching_app\lib\SessionManager;
use music_matching_app\lib\TokenManager;
use DateTime;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$ses = new SessionManager($db);

$loader = new \Twig\Loader\FilesystemLoader(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig\Environment($loader,[
    'cache' => Bootstrap::CACHE_DIR
]);

if(isset($_GET['url_token'])){
    $token = $_GET['url_token'];
    $token_type = 'account_regist';

    $tokenManager = new TokenManager($db);
    $accountInfo = $tokenManager->tokenExists($token, $token_type);

    $is_registerd = $accountInfo[0]['is_registerd'];
    $expiration_date = new DateTime($accountInfo[0]['expiration_date']);
    $res = $verifier->verifyToken($is_registerd, $expiration_date);
}
