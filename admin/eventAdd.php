<?php

namespace music_matching_app;

require_once dirname(__FILE__) . '/../Bootstrap.class.php';

use music_matching_app\lib\PDODatabase;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);

$loader = new \Twig\Loader\FilesystemLoader(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig\Environment($loader, [
    'cache' => Bootstrap::CACHE_DIR
]);

$member_id = 'admin';


$context = [];
echo $twig->render('adminEventAdd.html.twig', $context);
