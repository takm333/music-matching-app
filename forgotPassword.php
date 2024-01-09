<?php

namespace music_matching_app;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use music_matching_app\lib\PDODatabase;
use music_matching_app\lib\PasswordReset;
use music_matching_app\lib\SessionManager;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$ses = new SessionManager($db);

$loader = new \Twig\Loader\FilesystemLoader(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig\Environment($loader, [
    'cache' => Bootstrap::CACHE_DIR
]);

if(isset($_POST['mail_address'])) {
    $reset = new PasswordReset($db);
    $reset->resetPassword($_POST['mail_address']);
    $context['title'] = Bootstrap::PASSWORD_RESET_PAGE_TITLE;
    $context['sub_title'] = Bootstrap::PASSWORD_RESET_SUBTITLE;
    $context['text'] = Bootstrap::PASSWORD_RESET_TEXT;
    echo $twig->render('sendMail.html.twig', $context);
    exit();
}
$context = [];
echo $twig->render('forgotPassword.html.twig', []);
