<?php

require 'DBConnect.php';

$id = $_POST['id'];

try {
    $sql = "DELETE FROM news WHERE id = :id;";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    die();
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
