<?php

require 'DBConnect.php';
session_start();

$userID = $_SESSION['user_id'];
$time = date("Y-m-d H:i:s");

$check1 = isset($_POST['check_1']) ? 1 : 0;
$check2 = isset($_POST['check_2']) ? 1 : 0;
$check3 = isset($_POST['check_3']) ? 1 : 0;

$comment = htmlspecialchars($_POST['check_3_comment']);

$sql = "UPDATE `return_form_checker` SET 
`check_1` = :check1, 
`check_2` = :check2, 
`check_3` = :check3, 
`check_3_comment` = :check3comment, 
`cost` = :cost, 
`submitted` = 1, 
`submitted_date` = :submittedDate, 
`submitted_user` = :submittedUser 
WHERE `return_form_id` = :formid";

try {
    $stmt = $db->prepare($sql);

    $stmt->bindParam(':check1', $check1);
    $stmt->bindParam(':check2', $check2);
    $stmt->bindParam(':check3', $check3);
    $stmt->bindParam(':check3comment', $comment);
    $stmt->bindParam(':cost', $cost);
    $stmt->bindParam(':submittedDate', $time);
    $stmt->bindParam(':submittedUser', $userID);
    $stmt->bindParam(':formid', $_POST['return_form_id']);

    $stmt->execute();
    header('Location: form_handle_flatchecker.php');
    exit();
} catch (Exception $e) {
    echo "Error: {$e->getMessage()}";
    die();
}

