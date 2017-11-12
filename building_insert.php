<?php

require 'DBconnect.php';

  $building = $_POST['build1'];
  $room = $_POST['room1'];
  $floor = $_POST['floor1'];
  $compenrat = $_POST['com1'];

try {
  $stm = $db->prepare("INSERT INTO building (BuildingName,FloorCount,RoomCount,CompensationRate) Values (:build1,:floor1,:room1,:com1)");
  $stm->bindParam(':build1',$building);
  $stm->bindParam(':floor1',$floor);
  $stm->bindParam(':room1',$room);
  $stm->bindParam(':com1',$compenrat);
  $stm->execute();
  header('location:building.php');
    exit();

  }catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
  }





 ?>
