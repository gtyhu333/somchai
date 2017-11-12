<?php

require 'DBconnect.php';


$returnDate = implode('-', array_take($_POST, ['year', 'month', 'day']));

$db->beginTransaction();

try {
    $sql = "INSERT INTO `switch_form` (`RoomID`, `toRoomID`, ResidentID, SwitchingDate, `Cause`)
    VALUES (:roomid, :toroomid , :residentid, :switchdate, :cause);";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':roomid', $_POST['currentroom']);
    $stmt->bindParam(':toroomid', $_POST['roomid']);
    $stmt->bindParam(':residentid', $_POST['residentid']);
    $stmt->bindParam(':cause', $_POST['cause']);
    $stmt->bindParam(':switchdate', $returnDate);

    $stmt->execute();
} catch (Exception $e) {
    echo "Error: {$e->getMessage()}";
    $db->rollback();
    die();
}

$db->commit();

header('Location: index_member.php');
exit();
