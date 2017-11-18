<?php

require 'DBConnect.php';

session_start();
if (!isset($_SESSION["user_id"])){
  header('Location: login.php');
  die();
}

if ($_SESSION['user_type'] != 9) {
  header('Location: login.php');
  die();
}

$db->beginTransaction();

try {
    $stmt = $db->prepare('DELETE FROM `member` WHERE `UserID` = ?');
    $stmt->bindParam(1, $_POST['UserID']);
    $stmt->execute();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    $db->rollback();
    die();
}

$db->commit();

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
