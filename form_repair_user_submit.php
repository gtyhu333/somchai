<?php

require 'DBConnect.php';
require 'image_helpers.php';

session_start();
if (!isset($_SESSION["user_id"])){
  header('Location: login.php');
  die();
}

$user_id = $_SESSION['copy_from'] ? $_SESSION['copy_from'] : $_SESSION['user_id'];

try {
    $stmt = $db->prepare("SELECT * FROM v_resident WHERE `UserID` = ?");
    $stmt->bindParam(1, $user_id, PDO::PARAM_INT);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Error: {$e->getMessage()}";
    die();
}

$buildingid = isset($_POST['buildingid']) ? $_POST['buildingid'] : $user['BuildingID'];
$roomid = isset($_POST['roomid']) ? $_POST['roomid'] : $user['RoomID'];

$items = isset($_POST['item']) ? $_POST['item'] : [];

$isRepairRoom = empty($items) && isset($_POST['room']);

if ($_POST['otheritem'] != "") {
    $items[] = trim($_POST['otheritem']);
}

if (!empty($items)) {
  $items = implode(',', $items);
}

// insert to repairform
$db->beginTransaction();
try {
    $sql = "INSERT INTO `repairform` (`BuildingID`, `RoomID`, `UserID`, `Items`) 
    VALUES (:buildingid, :roomid, :userid, :items);";
    $stmt = $db->prepare($sql);

    $stmt->bindParam(':buildingid', $buildingid);
    $stmt->bindParam(':roomid', $roomid);
    $stmt->bindParam(':userid', $_SESSION['user_id']);
    if (is_string($items)) {
      $stmt->bindParam(':items', $items);
    } else {
      $item = 'room';
      $stmt->bindParam(':items', $item);
    }

    $stmt->execute();

    $repairId = $db->lastInsertId();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    $db->rollback();
    die();
}

if (! isset($_FILES) || $_FILES['pic']['name'][0] == "") {
    goto redirect;
}

$filespath = [];

foreach ($_FILES['pic']['name'] as $index => $file) {
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    $path = $_FILES['pic']['tmp_name'][$index];
    $filespath[] = resizeImage($path, 'jpg', 'repair_');
}

foreach ($filespath as $path) {
    try {
        $sql = "INSERT INTO `repairform_pic` (`form_id`, `path`) 
        VALUES (:formid, :filepath);";
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':formid', $repairId);
        $stmt->bindParam(':filepath', $path);

        $stmt->execute();

    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        $db->rollback();
        die();
    }
}

redirect:
// Change room status
if ($item == 'room') {
  try {
    $sql = "UPDATE `room` SET `RoomStatus` = 2 WHERE `RoomID` = ?";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(1, $roomid);
    $stmt->execute();  
  } catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    $db->rollback();
    die();
  }
}
// INSERT TO LOGS TABLE
try {
  $sql = "INSERT INTO event_logs (BuildingID, Type, EventID, UserID, Date) VALUES (:buildingid, :type, :eventid, :userid, :date)";
  $stmt = $db->prepare($sql);

  $type = "แจ้งซ่อม";
  $date = date("Y-m-d H:i:s");

  $stmt->bindParam(':buildingid', $buildingid);
  $stmt->bindParam(':type', $type);
  $stmt->bindParam(':eventid', $repairId);
  $stmt->bindParam(':userid', $user_id);
  $stmt->bindParam(':date', $date);

  $stmt->execute();
} catch (PDOException $e) {
  echo "Error: " . $e->getMessage();
  $db->rollback();
  die();
}

$db->commit();

if ($_SESSION['user_type'] == 9) {
    header('Location: form_repair_handle.php');
    exit();
}

if ($_SESSION['user_type'] == 3) {
    header('Location: index_committee.php');
    exit();
}

header('Location: index_member.php');
exit();
