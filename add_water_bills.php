<?php

require 'DBConnect.php';

// Get all data as array
$data = array_map('str_getcsv', file($_FILES['file']['tmp_name']));

// Get the headers and shift the data
$header = array_shift($data);

// Match the data with headers
$csv = array_map(function ($row) use ($header) {
    return array_combine($header, $row);
}, $data);

$db->beginTransaction();
foreach ($csv as $data) {
    try {
        $sql = "INSERT INTO water_bills (BuildingID, RoomID, MeterSerial, LastMonthCount, 
        ThisMonthCount, BasePrice, ServiceCharge, Vat, RecordDate, Month, Year) 
        VALUES (:buildingid, :roomid, :meterserial, :lastmonthcount, :thismonthcount, :baseprice, 
        :servicecharge, :vat, :recorddate, :month, :year);";

        $date = thaiDateToSqlFormat($data['recorddate']);
        $buildingID = $_POST['buildingid'];
        $roomid = getRoomID($data['room'], $buildingID, $db);

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':buildingid', $buildingID);
        $stmt->bindParam(':roomid', $roomid);
        $stmt->bindParam(':meterserial', $data['serial']);
        $stmt->bindParam(':lastmonthcount', $data['lastmnt']);
        $stmt->bindParam(':thismonthcount', $data['thismnt']);
        $stmt->bindParam(':baseprice', $data['price']);
        $stmt->bindParam(':servicecharge', $data['service']);
        $stmt->bindParam(':vat', $data['vat']);
        $stmt->bindParam(':recorddate', $date);
        $stmt->bindParam(':month', $_POST['month']);
        $stmt->bindParam(':year', $_POST['year']);
        $stmt->execute();

    } catch (Exception $e) {
        echo "Error: {$e->getMessage()} at line {$e->getLine()}";
        $db->rollback();
        die();
    }
}

$db->commit();
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;
