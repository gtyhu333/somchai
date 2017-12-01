<?php

require 'DBConnect.php';

$db->beginTransaction();

try {
    $stmt = $db->prepare('DELETE FROM `payments` WHERE `PaymentID` = ?');
    $stmt->bindParam(1, $_POST['id']);
    $stmt->execute();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    $db->rollback();
    die();
}

$db->commit();

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
