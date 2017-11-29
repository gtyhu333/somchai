<?php
require 'DBconnect.php';
session_start();

$user_id = $_POST['user_id'];

try {
  $stm = $db->prepare("SELECT * From member WHERE UserID = '$user_id';");
  $stm->execute();
  $result = $stm->fetch();

  if ($result) {
    $_SESSION["user_id"] = $result->UserID;
    $_SESSION["user_type"] = $result->UserType;
    $_SESSION["building_id"] = $result->BuildingID;
    $_SESSION["copy_from"] = $result->CopyFrom;

    switch ($result->UserType) {
      case 9:
        header('Location: index_admin.php');
        exit();
        break;
      case 1:
        header('Location: index_member.php');
        exit();
      case 2:
        header('Location: index_commit_flat.php');
        exit();
      case 3:
        header('Location: index_committee.php');
        exit();
      case 4:
        header('Location: index_flatchecker.php');
        exit();
      case 5:
        header('Location: index_flatmanager.php');
        exit();
        break;
    }
  }
} catch (PDOException $e) {
  echo "Fail" . $e->getMessage();
}
