<?php

require 'DBConnect.php';

try {
    $stmt = $db->prepare(
        "INSERT INTO payments (ResidentID, Month, Year, Electric, Water, Room, Sum, BuildingID) 
        VALUES (:residentid, :month, :year, :electric, :water, :room, :sum, :buildingid);"
    );

    $stmt->bindParam(':residentid', $_POST['residentid']);
    $stmt->bindParam(':month', $_POST['month']);
    $stmt->bindParam(':year', $_POST['year']);
    $stmt->bindParam(':electric', $_POST['electric']);
    $stmt->bindParam(':water', $_POST['water']);
    $stmt->bindParam(':room', $_POST['room']);
    $stmt->bindParam(':sum', $_POST['sum']);
    $stmt->bindParam(':buildingid', $_POST['buildingid']);

    $stmt->execute();

    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;

} catch (Exception $e) {
    echo "Error {$e->getMessage()} <br>";
    echo "{$e->getTraceAsString()}";
    die();
}
