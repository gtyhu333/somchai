<?php

require 'DBconnect.php';

  $buildingid = $_POST['build1'];
  $roomname = $_POST['roomname1'];
  $floor = $_POST['floor1'];
  $roomtype = $_POST['roomtype1'];
  $roomstatus = $_POST['roomstatus1'];
  $roomrate = $_POST['roomrate1'];
  $insurate = $_POST['insurate1'];

try {
  $stm = $db->prepare("INSERT INTO room (BuildingID,RoomName,Floor,RoomType,RoomStatus,RoomRate,InsurantRate)
                          Values (:build1,:roomname1,:floor1,:roomtype1,:roomstatus1,:roomrate1,:insurate1)");
  $stm->bindParam(':build1',$buildingid);
  $stm->bindParam(':roomname1',$roomname);
  $stm->bindParam(':floor1',$floor);
  $stm->bindParam(':roomtype1',$roomtype);
  $stm->bindParam(':roomstatus1',$roomstatus);
  $stm->bindParam(':roomrate1',$roomrate);
  $stm->bindParam(':insurate1',$insurate);
  $stm->execute();
  header('location:building.php');
    exit();

  }catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
  }





 ?>
