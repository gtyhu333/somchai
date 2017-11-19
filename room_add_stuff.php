<?php

require 'DBConnect.php';

$roomid = $_POST['id'];
$name = trim(htmlspecialchars($_POST['name']));

try {
    $sql = "INSERT INTO room_stuff (RoomID, Name) VALUES (:roomid, :name);";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':roomid', $roomid);
    $stmt->bindParam(':name', $name);
    $stmt->execute();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    die();
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
