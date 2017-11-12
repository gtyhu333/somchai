<?php
require 'DBconnect.php';

$user = $_POST['Username'];
$pass = $_POST['Pass'];
$userT = $_POST['Usertype'];
$position = $_POST['Position'];
$passhash = password_hash($pass,PASSWORD_DEFAULT);

//die(var_dump($passhash));
try {
    $stm = $db->prepare("INSERT INTO member (UserLogin,Passwd,UserType,PositionID) Values (:Username,:Pass,:Usertype,:Position)");
    $stm->bindParam(':Username',$user);
    $stm->bindParam(':Pass',$passhash);
    $stm->bindParam(':Usertype',$userT);
    $stm->bindParam(':Position',$position);
    $stm->execute();
    $result = $stm->fetch();

  } catch (PDOException $e) {
    echo "Fail" . $e->getMessage();
  }
?>
