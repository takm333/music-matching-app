<?php

namespace music_matching_app;

require_once dirname(__FILE__) . '/../Bootstrap.class.php';

use music_matching_app\lib\Csv;
use music_matching_app\lib\PDODatabase;
use music_matching_app\lib\File;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);

$loader = new \Twig\Loader\FilesystemLoader(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig\Environment($loader, [
    'cache' => Bootstrap::CACHE_DIR
]);

$member_id = 'admin';
if(isset($_FILES) && $_FILES['csv']['size'] > 0){
    if(File::isCsv($_FILES['csv'])){
        $fileName =  date('Ymd_His') . '_' . uniqid()  .'.csv';
        $filePath =  dirname(__FILE__) . '/upload_csv/' . $fileName;
        move_uploaded_file($_FILES['csv']['tmp_name'], $filePath);

        $csv = new Csv($db);
        $res = $csv->importEventCsv($filePath);

        if($res === true){
            echo true;
        }else{
            echo $res;
        }
    }
}
