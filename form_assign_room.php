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

try {
  $stmt = $db->prepare("SELECT * FROM room WHERE RoomID = ? LIMIT 1;");
  $stmt->bindParam(1, $_POST['roomID'], PDO::PARAM_INT);
  $stmt->execute();
  $stmt->setFetchMode(PDO::FETCH_ASSOC);
  $room = $stmt->fetch();
}
catch (PDOException $e) {
  echo "Error: " . $e->getMessage();
  die();
}

try {
  $stmt = $db->prepare("SELECT * FROM staff WHERE StaffID = ? LIMIT 1;");
  $stmt->bindParam(1, $_POST['StaffID'], PDO::PARAM_INT);
  $stmt->execute();
  $stmt->setFetchMode(PDO::FETCH_ASSOC);
  $request = $stmt->fetch();
}
catch(PDOException $e) {
  echo "Error: " . $e->getMessage();
  die();
}

$db->beginTransaction();

// Insert to member table
try {
  $sql = "INSERT INTO `member` 
  (`BuildingID`, `UserPNameT`, `UserNameT`, `UserSNameT`, `PositionID`
   , `PersonnelType`, `FacID`, `DeptID`, `LivingCount`, `Checkin_date`) 
   VALUES (:buildingid, :userpnamet, :usernamet, :usersnamet, :positionid, :personeltype
   , :facid, :deptid, :livingcount, '0000-00-00 00:00:00');";

  $stmt = $db->prepare($sql);
  $livingcount = 0;

  $stmt->bindParam(':buildingid', $room['BuildingID']);
  $stmt->bindParam(':userpnamet', $request['Pname']);
  $stmt->bindParam(':usernamet', $request['Name']);
  $stmt->bindParam(':usersnamet', $request['Surname']);
  $stmt->bindParam(':positionid', $request['PositionID']);
  $stmt->bindParam(':personeltype', $request['PersonnelType']);
  $stmt->bindParam(':facid', $request['FacID']);
  $stmt->bindParam(':deptid', $request['DeptID']);
  $stmt->bindParam(':livingcount', $livingcount, PDO::PARAM_INT);

  $stmt->execute();

  $memeberID = $db->lastInsertId();
} catch (PDOException $e) {
  echo "Error: " . $e->getMessage();
  $db->rollback();
  die();
}

// Insert to resident table
try {
  $sql = "INSERT INTO `resident` 
  (`RoomID`, `UserID`, `StartDate`, `EndDate`) 
   VALUES (:roomid, :userid, now(), 0000-00-00);";

  $stmt = $db->prepare($sql);

  $stmt->bindParam(':roomid', $room['RoomID']);
  $stmt->bindParam(':userid', $memeberID);
  
  $stmt->execute();
  $residentID = $db->lastInsertId();
} catch (PDOException $e) {
  echo "Error: " . $e->getMessage();
  $db->rollback();
  die();
}

// Update room table
try {
  $sql = "UPDATE `room` SET `RoomStatus` = 3 WHERE `RoomID` = :roomid;";
  $stmt = $db->prepare($sql);

  $stmt->bindParam(':roomid', $room['RoomID']);

  $stmt->execute();
} catch (PDOException $e) {
  echo "Error: " . $e->getMessage();
  $db->rollback();
  die();
}

// Update staff table
try {
  $sql = "UPDATE `staff` SET `request_status` = 'จัดสรร' WHERE `StaffID` = :staffid;";
  $stmt = $db->prepare($sql);

  $stmt->bindParam(':staffid', $request['StaffID']);

  $stmt->execute();
} catch (PDOException $e) {
  echo "Error: " . $e->getMessage();
  $db->rollback();
  die();
}

$db->commit();

// INSERT TO LOGS TABLE
try {
  $sql = "INSERT INTO event_logs (BuildingID, Type, EventID, UserID, Date) VALUES (:buildingid, :type, :eventid, :userid, :date)";
  $stmt = $db->prepare($sql);

  $type = "ได้รับการจัดสรรห้อง";
  $date = date("Y-m-d H:i:s");

  $stmt->bindParam(':buildingid', $room['BuildingID']);
  $stmt->bindParam(':type', $type);
  $stmt->bindParam(':eventid', $residentID);
  $stmt->bindParam(':userid', $memeberID);
  $stmt->bindParam(':date', $date);

  $stmt->execute();
} catch (PDOException $e) {
  echo "Error: " . $e->getMessage();
  $db->rollback();
  die();
}

// redirect back
header('Location: form_handle.php');
exit();
