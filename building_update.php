<?php

require 'DBconnect.php';

  $building = $_POST['build1'];
  $room = $_POST['room1'];
  $floor = $_POST['floor1'];
  $compenrat = $_POST['com1'];
  $id = $_POST['id'];


try {
  $stm = $db->prepare("UPDATE building SET BuildingName = :build1 , RoomCount = :room1 ,FloorCount = :floor1
    ,CompensationRate = :com1 WHERE BuildingID = :id");
  $stm->bindParam(':build1',$building);
  $stm->bindParam(':room1',$room);
  $stm->bindParam(':floor1',$floor);
  $stm->bindParam(':com1',$compenrat);
  $stm->bindParam(':id',$id);

  $stm->execute();
  header('location:building.php');
    exit();

  }catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
  }





 ?>
