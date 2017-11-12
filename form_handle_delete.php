<?php
require 'DBconnect.php';

$id = $_POST['StaffID'];

try {
  $stmt = $db->prepare("DELETE From staff WHERE StaffID = :del ");
  $stmt->bindParam(':del',$id);
  $stmt->execute();
  header('Location: form_handle.php');
  exit();
}catch(PDOException $e) {
  echo "Error: " . $e->getMessage();
}
