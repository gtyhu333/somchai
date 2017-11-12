<?php
require 'DBconnect.php';

  $id = $_POST['id'];

  try {
    $stmt = $db->prepare("DELETE From building WHERE BuildingID = :del ");
    $stmt->bindParam(':del',$id);
    $stmt->execute();
    header('Location: building.php');
    exit();
  }catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
  }

 ?>
Chat Conversation End
Type a message...
