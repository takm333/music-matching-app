<?php

namespace music_matching_app;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use music_matching_app\lib\initMaster;
use music_matching_app\lib\PDODatabase;
use music_matching_app\lib\Profile;
use music_matching_app\lib\SessionManager;
use music_matching_app\lib\UserValidator;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$ses = new SessionManager($db);

$loader = new \Twig\Loader\FilesystemLoader(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig\Environment($loader, [
    'cache' => Bootstrap::CACHE_DIR
]);
$ses->checkSession();
if(! isset($_SESSION['member_id'])) {
    header('Location: ' . Bootstrap::ENTRY_URL . 'login.php');
}

$init = new initMaster($db);
[$genderArr, $ageArr, $areaArr, $genreArr] = $init->initProfileList();

$member_id = isset($_SESSION['member_id']) ? $_SESSION['member_id'] : '';
$is_login = ($member_id !== '') ? true : false;

if($member_id === '') {
    header('Location: ' . Bootstrap::ENTRY_URL . 'eventlist.php');
    exit();
}

$profile = new Profile($db);

$dataArr = [];
$errArr = [];

if(isset($_POST['update_button']) && $_POST['update_button'] === 'update') {
    $validator = new UserValidator($db);

    $tmpImage = (isset($_FILES) && ! empty($_FILES['image']['size'])) ? $_FILES['image'] : '';
    $dataArr = $_POST;
    unset($dataArr['update_button']);

    $errArr = $validator->profileCheckError($dataArr, $tmpImage);
    $errFlg = $validator->getErrFlg();

    if($errFlg) {
        //登録処理
        if($tmpImage !== '') {
            $imageInfo = getimagesize($tmpImage['tmp_name']);
            $extension = str_replace('image/', '.', $imageInfo['mime']);
            $imageName = uniqid('', true) . $extension;
            move_uploaded_file($tmpImage['tmp_name'], './user_image/' . $imageName);
        } else {
            $imageName = '';
        }

        $profile->updateProfile($member_id, $dataArr, $imageName);
        header('Location: ' . Bootstrap::ENTRY_URL . 'myProfile.php');
        exit;
    }
}

$display = '';

$myProfile = [];
$eventList = [];

if(isset($_GET['display'])) {
    $display = $_GET['display'];
    if($display === '') {
        $myProfile = $profile->searchMyProfile($member_id);
    } elseif($display === 'update') {
        $myProfile = $profile->searchMyProfile($member_id);
    } elseif($display === 'past') {
        $than = '<';
        $eventList = $profile->searchParticipateEvent($member_id, $than);
    } elseif($display === 'future') {
        $than = '>';
        $eventList = $profile->searchParticipateEvent($member_id, $than);
    } else {
        header('Location: ' . Bootstrap::ENTRY_URL . 'myProfile.php');
        exit();
    }
} else {
    $myProfile = $profile->searchMyProfile($member_id);
}

$context = [];
$context['display'] = $display;
$context['genderArr'] = $genderArr;
$context['ageArr'] = $ageArr;
$context['areaArr'] = $areaArr;
$context['genreArr'] = $genreArr;
$context['is_login'] = $is_login;
$context['dataArr'] = $dataArr;
$context['errArr'] = $errArr;
$context['myProfile'] = (! empty($myProfile)) ? $myProfile : [];
$context['eventList'] = (! empty($eventList)) ? $eventList : [];

echo $twig->render('myProfile.html.twig', $context);
