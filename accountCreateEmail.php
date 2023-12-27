<?php

namespace music_matching_app;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use music_matching_app\lib\PDODatabase;
use music_matching_app\lib\AccountCreateEmail;
use music_matching_app\lib\SessionManager;
use music_matching_app\lib\UserValidator;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$ses = new SessionManager($db);
$account = new AccountCreateEmail($db);
$dataArr = [];
$errArr = [];

$loader = new \Twig\Loader\FilesystemLoader(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig\Environment($loader,[
    'cache' => Bootstrap::CACHE_DIR
]);

if(isset($_POST['mail_address']) && isset($_POST['password'])){
    $dataArr = $_POST;

    $validator = new UserValidator($db);
    $validator->accountExists($dataArr);
    $errArr = $validator->checkError($dataArr);
    $errFlg = $validator->getErrFlg();
    if($errFlg){
        $res = $account->registPreAccount($dataArr['mail_address'], $dataArr['password']);
        if($res){
            echo $twig->render('provisionalRegistration.html.twig');
            exit();
        }else{
            echo $twig->render('failed.html.twig');
            exit();
        }
    }
}
$context = [];
$context['dataArr'] = $dataArr;
$context['errArr'] = $errArr;
echo $twig->render('accountCreateEmail.html.twig',$context);
