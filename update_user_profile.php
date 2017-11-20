<?php

require 'DBConnect.php';

session_start();
if (!isset($_SESSION["user_id"])){
    header('Location: login.php');
    die();
}

$db->beginTransaction();

try {
    $sql = "UPDATE `member` SET 
    `UserPNameT` = :userpnamet, 
    `UserNameT` = :usernamet, 
    `UserSNameT` = :usersnamet,
    `Email` = :email, 
    `Phone` = :phone
    WHERE `UserID` = :userid;";

    $stmt = $db->prepare($sql);

    $stmt->bindParam(':userid', $_POST['userid']);
    $stmt->bindParam(':userpnamet', trim($_POST['userpnamet']));
    $stmt->bindParam(':usernamet', trim($_POST['usernamet']));
    $stmt->bindParam(':usersnamet', trim($_POST['usersnamet']));
    $stmt->bindParam(':email', trim($_POST['email']));
    $stmt->bindParam(':phone', trim($_POST['phone']));

    $stmt->execute();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    $db->rollback();
    die();
}

$db->commit();

header('Location: edit_profile.php');
exit();
