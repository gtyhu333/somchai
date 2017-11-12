<?php

require 'DBConnect.php';

// dd($_POST);

if ($_POST['newstatus'] != 2) {
    goto changestatus;
}

$defaultDate = '0000-00-00';
$status = 1;

$db->beginTransaction();

// Get the form's data
try {
    $sql = "SELECT * FROM switch_form WHERE SwitchID = :switchid LIMIT 1";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':switchid', $_POST['switchid']);
    $stmt->execute();

    $form = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Error: {$e->getMessage()}";
    echo "<pre>{$e->getTraceAsString()}</pre>";
    $db->rollback();
    die();
}

// Get the requester info.
try {
    $sql = "SELECT * FROM resident WHERE ResidentID = :residentid";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':residentid', $form['ResidentID']);

    $stmt->execute();
    $resident1 = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Error: {$e->getMessage()}";
    echo "<pre>{$e->getTraceAsString()}</pre>";
    $db->rollback();
    die();
}


// Check if room is occupied.
try {
    $sql = "SELECT * FROM resident WHERE RoomID = :roomid";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':roomid', $form['toRoomID']);

    $stmt->execute();
    $roomIsOccupied = $resident2 = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Error: {$e->getMessage()}";
    echo "<pre>{$e->getTraceAsString()}</pre>";
    $db->rollback();
    die();
}

if ($roomIsOccupied) {

    try {
        // Update the resident status for resident2
        $sql = "UPDATE resident SET EndDate = :endate, ActiveStatus = '0' WHERE ResidentID = :residentid;";
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':endate', $form['SwitchingDate']);
        $stmt->bindParam(':residentid', $resident2['ResidentID']);
        $stmt->execute();

        // Insert the new one to the table
        $sql = "
        INSERT INTO resident (RoomID, UserID, StartDate, EndDate, ActiveStatus) 
        VALUES (:roomid, :userid, :startdate, :enddate, :status);
        ";

        $stmt = $db->prepare($sql);

        $stmt->bindParam(':roomid', $form['RoomID']);
        $stmt->bindParam(':userid', $resident2['UserID']);
        $stmt->bindParam(':startdate', $form['SwitchingDate']);
        $stmt->bindParam(':enddate', $defaultDate);
        $stmt->bindParam(':status', $status);
        $stmt->execute();

    } catch (Exception $e) {
        echo "Error: {$e->getMessage()}";
        echo "<pre>{$e->getTraceAsString()}</pre>";
        $db->rollback();
        die();
    }
}

try {
    // Update the resident status for resident1
    $sql = "UPDATE resident SET EndDate = :endate, ActiveStatus = '0' WHERE ResidentID = :residentid;";
    $stmt = $db->prepare($sql);

    $stmt->bindParam(':endate', $form['SwitchingDate']);
    $stmt->bindParam(':residentid', $resident1['ResidentID']);
    $stmt->execute();

    // Insert the new one to the table
    $sql = "
    INSERT INTO resident (RoomID, UserID, StartDate, EndDate, ActiveStatus) 
    VALUES (:roomid, :userid, :startdate, :enddate, :status);
    ";

    $stmt = $db->prepare($sql);

    $stmt->bindParam(':roomid', $form['toRoomID']);
    $stmt->bindParam(':userid', $resident1['UserID']);
    $stmt->bindParam(':startdate', $form['SwitchingDate']);
    $stmt->bindParam(':enddate', $defaultDate);
    $stmt->bindParam(':status', $status);
    $stmt->execute();
} catch (Exception $e) {
    echo "Error: {$e->getMessage()}";
    echo "<pre>{$e->getTraceAsString()}</pre>";
    $db->rollback();
    die();
}

changestatus:
// dd('Hi!');
// Change the status of the form
try {
    $sql = "UPDATE switch_form SET Status = :status WHERE SwitchID = :switchid";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':switchid', $_POST['switchid']);
    $stmt->bindParam(':status', $_POST['newstatus']);

    $stmt->execute();
} catch (Exception $e) {
    echo "Error: {$e->getMessage()}";
    echo "<pre>{$e->getTraceAsString()}</pre>";
    $db->rollback();
    die();
}

$db->commit();
header('Location: form_switch_handle_admin.php');
exit();

