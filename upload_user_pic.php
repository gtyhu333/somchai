<?php

require 'DBConnect.php';

session_start();
$user_id = $_SESSION['copy_from'] ? $_SESSION['copy_from'] : $_SESSION['user_id'];

$file = $_FILES['newpic'];

if (!file_exists('userpic')) {
    mkdir('userpic', 0777, true);
}

switch (strtolower(pathinfo($file['name'], PATHINFO_EXTENSION ))) {
    case 'jpeg':
    case 'jpg':
        $source = imagecreatefromjpeg($file['tmp_name']);
    break;

    case 'png':
        $source = imagecreatefrompng($file['tmp_name']);
    break;

    case 'gif':
        $source = imagecreatefromgif($file['tmp_name']);
    break;
}

list($width, $height) = getimagesize($file['tmp_name']);
$newheight = 160;
$newwidth = 160;

$destination = imagecreatetruecolor($newwidth, $newheight);
imagecopyresampled($destination, $source, 0,0,0,0, $newwidth, $newheight, $width, $height);

$filename = __DIR__ . '/userpic/' . $user_id . '.jpg';
imagejpeg($destination, $filename, 100);

header('Location: edit_profile.php');
exit();
