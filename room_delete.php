<?php
require 'DBconnect.php';

  $id = $_POST['id'];

  try {
    $stmt = $db->prepare("SELECT BuildingID From room WHERE RoomID = :del ");
    $stmt->bindParam(':del',$id);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $result = $stmt->fetchAll()[0];

    $buildingID = $result['BuildingID'];
  
    $stmt = $db->prepare("DELETE From room WHERE RoomID = :del ");
    $stmt->bindParam(':del',$id);
    $stmt->execute();
    header('Location: building.php?building=' . $buildingID);
    exit();
  }catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
  }

 ?>
