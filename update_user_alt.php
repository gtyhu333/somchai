<?php

require 'DBConnect.php';

session_start();
if (!isset($_SESSION["user_id"])){
    header('Location: login.php');
    die();
}

$db->beginTransaction();

try {
    $sql = "UPDATE `member` SET `UserPNameT` = :userpnamet, `UserNameT` = :usernamet, 
    `UserSNameT` = :usersnamet, `FacID` = :facid, `DeptID` = :deptid WHERE `UserID` = :userid;";

    $stmt = $db->prepare($sql);

    $stmt->bindParam(':userid', $_POST['userid']);
    $stmt->bindParam(':userpnamet', $_POST['userpnamet']);
    $stmt->bindParam(':usernamet', $_POST['usernamet']);
    $stmt->bindParam(':usersnamet', $_POST['usersnamet']);
    $stmt->bindParam(':facid', $_POST['facid']);
    $stmt->bindParam(':deptid', $_POST['deptid']);

    $stmt->execute();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    $db->rollback();
    die();
}

$db->commit();

header('Location: member.php');
exit();
