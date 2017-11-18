<?php

require 'DBConnect.php';

session_start();
if (!isset($_SESSION["user_id"])){
  header('Location: login.php');
  die();
}

if ($_SESSION['user_type'] != 9) {
  header('Location: login.php');
  die();
}

$db->beginTransaction();

$stmt = $db->prepare('SELECT * FROM `member` WHERE `UserID` = ?;');
$stmt->bindParam(1, $_POST['userid']);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user['UserLogin']) {
  goto update_user;
}

try {
  // Create temp table
  $stmt = $db->prepare('CREATE TEMPORARY TABLE `tmp` SELECT * FROM `member` WHERE `UserID` = ?;');

  $stmt->bindParam(1, $_POST['userid']);
  $stmt->execute();

  // $stmt = $db->prepare('ALTER TABLE tmp DROP COLUMN UserID;');
  // $stmt->execute();

  // Update the record !
  $sql = "UPDATE `tmp` SET `UserID` = 0, `UserLogin` = :userlogin, `Passwd` = :passwd, `UserType` = :usertype, `CopyFrom` = :userid";
  $stmt = $db->prepare($sql);
  $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

  $stmt->bindParam(':userid', $_POST['userid']);
  $stmt->bindParam(':userlogin', $_POST['username']);
  $stmt->bindParam(':passwd', $pass);
  $stmt->bindParam(':usertype', $_POST['usertype']);

  $stmt->execute();

  // INSERT INTO actual member table
  $stmt = $db->prepare('INSERT INTO member SELECT * FROM tmp;');
  $stmt->execute();

  // Delete tmp table
  $stmt = $db->prepare('DROP TABLE tmp');
  $stmt->execute();

} catch (PDOException $e) {
  echo "Error: " . $e->getMessage();
  $db->rollback();
  die();
}

$db->commit();
goto redirect;

update_user:
$hasPass = $_POST['password'] != '';

try {
    $sql = "UPDATE `member` SET `UserLogin` = :userlogin, `UserType` = :usertype";

    if ($hasPass) {
        $sql .= ", `Passwd` = :passwd";
    }

    $sql .= " WHERE `UserID` = :userid;";

    $stmt = $db->prepare($sql);

    $stmt->bindParam(':userid', $_POST['userid']);
    $stmt->bindParam(':userlogin', $_POST['username']);
    if ($hasPass) {
        $stmt->bindParam(':passwd', password_hash($_POST['password'], PASSWORD_DEFAULT));
    }
    $stmt->bindParam(':usertype', $_POST['usertype']);

    $stmt->execute();
} catch (PDOException $e) {
  echo "Error: " . $e->getMessage();
  $db->rollback();
  die();
}

$db->commit();

// redirect back
redirect:
header('Location: member.php');
exit();
