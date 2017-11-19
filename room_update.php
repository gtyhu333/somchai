<?php

require 'DBconnect.php';

$roomid = $_POST['roomid1'];
$buildingid = $_POST['build1'];
$roomname = $_POST['roomname1'];
$floor = $_POST['floor1'];
$roomtype = $_POST['roomtype1'];
$roomstatus = $_POST['roomstatus1'];
$roomrate = $_POST['roomrate1'];
$insurate = $_POST['insurate1'];

$stmt = $db->prepare("SELECT BuildingID From room WHERE RoomID = :del ");
$stmt->bindParam(':del', $roomid);
$stmt->execute();
$stmt->setFetchMode(PDO::FETCH_ASSOC);
$result = $stmt->fetch();

$buildingID = $result['BuildingID'];

// die(var_dump($_POST));

try {
  $stm = $db->prepare("UPDATE room SET BuildingID = :build1 ,RoomName = :roomname1 , floor = :floor1 , RoomType =:roomtype1 ,
    RoomStatus = :roomstatus1 ,RoomRate = :roomrate1 , InsurantRate = :insurate1 WHERE RoomID = :roomid2");
    //$stm->bindParam(':roomid2',$roomid);
    $stm->bindParam(':build1' ,$buildingid);
    $stm->bindParam(':roomname1',$roomname);
    $stm->bindParam(':floor1',$floor);
    $stm->bindParam(':roomtype1',$roomtype);
    $stm->bindParam(':roomstatus1',$roomstatus);
    $stm->bindParam(':roomrate1',$roomrate);
    $stm->bindParam(':insurate1',$insurate);
    $stm->bindParam(':roomid2',$roomid);
    $stm->execute();
    header('location:building.php?building=' . $buildingID);
    exit();

  }catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
  }





 ?>
