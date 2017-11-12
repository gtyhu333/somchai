<?php

require 'DBConnect.php';

if (isset($_POST['repair'])) {
    $sql = "UPDATE `repairform` SET `Status` = 2 WHERE id = ?";
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

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
