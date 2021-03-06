<?php

require 'DBConnect.php';

session_start();
if (!isset($_SESSION["user_id"])){
  header('Location: login.php');
  die();
}

$hasPass = $_POST['password'] != '';

$db->beginTransaction();

try {
    $sql = "UPDATE `member` SET `UserLogin` = :userlogin, `UserType` = :usertype";

    if ($hasPass) {
        $sql .= ", `Passwd` = :passwd";
    }

    $sql .= " WHERE `UserID` = :userid;";

    $stmt = $db->prepare($sql);

    $stmt->bindParam(':userid', $_POST['userid']);
    $stmt->bindParam(':userlogin', $_POST['username']);
    if ($hasPass) {
        $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt->bindParam(':passwd', $pass);
    }
    $stmt->bindParam(':usertype', $_POST['usertype']);

    $stmt->execute();
} catch (PDOException $e) {
  echo "Error: " . $e->getMessage();
  $db->rollback();
  die();
}

$db->commit();

// redirect back
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
