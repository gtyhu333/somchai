<?php

require 'DBConnect.php';

try {
    $stmt = $db->prepare(
        "INSERT INTO payments (ResidentID, Month, Year) VALUES (:residentid, :month, :year);"
    );

    $stmt->bindParam(':residentid', $_POST['residentid']);
    $stmt->bindParam(':month', $_POST['month']);
    $stmt->bindParam(':year', $_POST['year']);

    $stmt->execute();

    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;

} catch (Exception $e) {
    echo "Error {$e->getMessage()} <br>";
    echo "{$e->getTraceAsString()}";
    die();
}
