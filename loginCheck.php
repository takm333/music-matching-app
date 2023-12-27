<?php

namespace music_matching_app;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use music_matching_app\lib\PDODatabase;
use music_matching_app\lib\SessionManager;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$ses = new SessionManager($db);

$ses->checkSession();
$member_id = isset($_SESSION['member_id']) ? $_SESSION['member_id'] : '';
$is_login = ($member_id !== '') ? true : false;
echo json_encode(['is_login' => $is_login]);
