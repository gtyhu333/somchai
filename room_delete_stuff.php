<?php

require 'DBConnect.php';

$stuffid = $_POST['id'];

try {
    $sql = "DELETE FROM room_stuff WHERE id = :id;";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $stuffid);
    $stmt->execute();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    die();
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
