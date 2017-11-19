<?php

require 'DBConnect.php';

$stuffid = $_POST['id'];
$name = trim(htmlspecialchars($_POST['name']));

try {
    $sql = "UPDATE room_stuff SET Name = :name WHERE id = :id;";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $stuffid);
    $stmt->bindParam(':name', $name);
    $stmt->execute();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    die();
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
