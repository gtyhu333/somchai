<?php

require 'DBConnect.php';

if (isset($_POST['repair'])) {
    $sql = "UPDATE `repairform` SET `Status` = {$_POST['newstatus']} WHERE id = ?";
}

if (isset($_POST['delete'])) {
    $sql = "DELETE FROM `repairform` WHERE id = ?";
}

try {
    $stmt = $db->prepare($sql);
    $stmt->bindParam(1, $_POST['id'], PDO::PARAM_INT);
    $stmt->execute();
} catch (Exception $e) {
    echo "Error: {$e->getMessage()}";
    die();
}

if (isset($_POST['newstatus']) && $_POST['newstatus'] == 2) {
    $sql = "SELECT * FROM repairform WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(1, $_POST['id'], PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['Items'] == 'room') {
        $sql = "UPDATE `room` SET `RoomStatus` = 1 WHERE `RoomID` = ?";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(1, $result['RoomID']);
        $stmt->execute();
    }
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
