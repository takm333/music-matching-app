<?php

namespace music_matching_app;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use music_matching_app\lib\PDODatabase;
use music_matching_app\lib\AccountVerifier;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);

$loader = new \Twig\Loader\FilesystemLoader(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig\Environment($loader,[
    'cache' => Bootstrap::CACHE_DIR
]);

if(isset($_GET['url_token'])){
    $url_token = $_GET['url_token'];

    $verifier = new AccountVerifier($db);
    $is_verify = $verifier->verifyAccount($url_token);

    if($is_verify){
        $verifier->registAccount();
    }
}
