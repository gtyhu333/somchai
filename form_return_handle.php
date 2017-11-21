<?php

require 'DBConnect.php';

session_start();
$user_id = $_SESSION['copy_from'] ? $_SESSION['copy_from'] : $_SESSION['user_id'];

$returnDate = implode('-', array_take($_POST, ['year', 'month', 'day']));

$db->beginTransaction();
try {
    $sql = "INSERT INTO `return_form` (`ResidentID`, `ReturnDate`, `Cause`) 
    VALUES (:residentid, :returndate , :cause);";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':residentid', $_POST['residentid']);
    $stmt->bindParam(':returndate', $returnDate);
    $stmt->bindParam(':cause', $_POST['cause']);

    $stmt->execute();

    $returnFormID = $db->lastInsertId();

// Add form for flat checker

    $sql = "INSERT INTO `return_form_checker` (`return_form_id`) 
    VALUES (:formid);";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':formid', $returnFormID);

    $stmt->execute();

// Add form for flat manager

    $sql = "INSERT INTO `return_form_manager` (`return_form_id`) 
    VALUES (:formid);";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':formid', $returnFormID);

    $stmt->execute();

} catch (Exception $e) {
    echo "Error: {$e->getMessage()}";
    $db->rollback();
    die();
}

// INSERT TO LOGS TABLE
try {
  $sql = "INSERT INTO event_logs (BuildingID, Type, EventID, UserID, Date) VALUES (:buildingid, :type, :eventid, :userid, :date)";
  $stmt = $db->prepare($sql);

  $type = "ขอคืนห้อง";
  $date = date("Y-m-d H:i:s");

  $stmt->bindParam(':buildingid', $_SESSION['building_id']);
  $stmt->bindParam(':type', $type);
  $stmt->bindParam(':eventid', $returnFormID);
  $stmt->bindParam(':userid', $user_id);
  $stmt->bindParam(':date', $date);

  $stmt->execute();
} catch (PDOException $e) {
  echo "Error: " . $e->getMessage();
  $db->rollback();
  die();
}

$db->commit();

// dd($db->errorInfo());

header('Location: index_member.php');
exit();
