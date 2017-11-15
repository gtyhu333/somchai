<?php
require 'DBconnect.php';

$user = $_POST['user'];
$pass = $_POST['pass'];

try {
  $stm = $db->prepare("SELECT * From member WHERE UserLogin = '$user';");
  $stm->execute();
  $result = $stm->fetch();

  if ($result) {
    if (password_verify($pass,$result->Passwd)) {
      session_start();
      $_SESSION["user_id"] = $result->UserID;
      $_SESSION["user_type"] = $result->UserType;
      switch ($result->UserType) {
        case 9:
          header('Location: building.php');
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
          break;
        case 4:
          header('Location: index_flatchecker.php');
          exit();
        case 5:
          header('Location: index_flatmanager.php');
          exit();
          break;
        case 6:
          $_SESSION["Error"] = 'User นี้ไม่สามารถ Login ได้';
          unset($_SESSION["user_id"]);
          unset($_SESSION["user_type"]);
          header('Location: login.php');
          exit();
          break;
      }
    } else {
      session_start();
      $_SESSION["Error"] = 'Invalid username or password.';
      header('Location: login.php');
    }
  } else {
    session_start();
    $_SESSION["Error"] = 'Invalid username or password.';
    header('Location: login.php');
  }
} catch (PDOException $e) {
  echo "Fail" . $e->getMessage();
}


?>
