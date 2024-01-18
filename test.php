<?php

namespace music_matching_app\lib;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use finfo;
use music_matching_app\lib\Csv;
use music_matching_app\lib\PDODatabase;
use music_matching_app\Bootstrap;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);

$loader = new \Twig\Loader\FilesystemLoader(Bootstrap::TEMPLATE_DIR);

$csv = new Csv($db);
// $csv->importEventCsv(dirname(__FILE__) . '/admin/upload_csv/' . 'event_import_format_20240115_161032_65a4da6860834.csv');
$filePath = (dirname(__FILE__) . '/admin/upload_csv/' . '20240115_155514_65a4d6d275644.csv');
// $finfo = new finfo();
// $mimeType = $finfo->file($filePath, FILEINFO_MIME_TYPE);
// var_dump($mimeType);


$csv = new Csv($db);
$res = $csv->importEventCsv($filePath);
var_dump($res);
