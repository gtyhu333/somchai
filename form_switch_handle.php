<?php

require 'DBconnect.php';

session_start();

$returnDate = implode('-', array_take($_POST, ['year', 'month', 'day']));

$user_id = $_SESSION['copy_from'] ? $_SESSION['copy_from'] : $_SESSION['user_id'];

$db->beginTransaction();

try {
    $sql = "INSERT INTO `switch_form` (`RoomID`, `toRoomID`, ResidentID, SwitchingDate, `Cause`)
    VALUES (:roomid, :toroomid , :residentid, :switchdate, :cause);";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':roomid', $_POST['currentroom']);
    $stmt->bindParam(':toroomid', $_POST['roomid']);
    $stmt->bindParam(':residentid', $_POST['residentid']);
    $stmt->bindParam(':cause', $_POST['cause']);
    $stmt->bindParam(':switchdate', $returnDate);

    $stmt->execute();
    $switchId = $db->lastInsertId();
} catch (Exception $e) {
    echo "Error: {$e->getMessage()}";
    $db->rollback();
    die();
}

// INSERT TO LOGS TABLE
try {
  $sql = "INSERT INTO event_logs (BuildingID, Type, EventID, UserID, Date) VALUES (:buildingid, :type, :eventid, :userid, :date)";
  $stmt = $db->prepare($sql);

  $type = "ขอย้าย";
  $date = date("Y-m-d H:i:s");

  $stmt->bindParam(':buildingid', $_SESSION['building_id']);
  $stmt->bindParam(':type', $type);
  $stmt->bindParam(':eventid', $switchId);
  $stmt->bindParam(':userid', $user_id);
  $stmt->bindParam(':date', $date);

  $stmt->execute();
} catch (PDOException $e) {
  echo "Error: " . $e->getMessage();
  $db->rollback();
  die();
}

$db->commit();

header('Location: index_member.php');
exit();
