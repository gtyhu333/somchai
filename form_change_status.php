<?php
require 'DBconnect.php';

$id = $_POST['StaffID'];
$newstatus = $_POST['newstatus'];

try {
  $stmt = $db->prepare("UPDATE staff SET request_status = :status WHERE StaffID = :id ");
  $stmt->bindParam(':status',$newstatus);
  $stmt->bindParam(':id',$id);
  $stmt->execute();
  header('Location: form_handle.php');
  exit();
}catch(PDOException $e) {
  echo "Error: " . $e->getMessage();
}
