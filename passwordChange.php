<?php

namespace music_matching_app;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use music_matching_app\lib\PDODatabase;
use music_matching_app\lib\TokenVerifier;
use music_matching_app\lib\PasswordChange;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);

$loader = new \Twig\Loader\FilesystemLoader(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig\Environment($loader,[
    'cache' => Bootstrap::CACHE_DIR
]);

if(isset($_GET['password_reset_token'])){
    $password_reset_token = $_GET['password_reset_token'];
    $verifier = new TokenVerifier($db);
    $verifier->verifyToken($password_reset_token);
}
$context = [];
echo $twig->render('accountCreate.html.twig',[]);
