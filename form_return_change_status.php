<?php

require 'DBConnect.php';

$db->beginTransaction();

// dd($_POST);

try {
    $sql = "UPDATE return_form SET Status = :status WHERE ReturnID = :returnid";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':returnid', $_POST['returnid']);
    $stmt->bindParam(':status', $_POST['newstatus']);

    $stmt->execute();
} catch (Exception $e) {
    echo "Error: {$e->getMessage()}";
    $db->rollback();
    die();
}

if ($_POST['newstatus'] != 2) {
    $db->commit();
    header('Location: form_return_handle_admin.php');
    exit();
}

try {
    $sql = "SELECT return_form.ResidentID, resident.UserID, return_form.ReturnDate FROM return_form 
    INNER JOIN resident ON return_form.ResidentID = resident.residentID 
    WHERE ReturnID = :returnid";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':returnid', $_POST['returnid']);

    $stmt->execute();
    list($residentID, $userID, $returnDate) = $stmt->fetch(PDO::FETCH_NUM);

    $sql = "UPDATE resident SET EndDate = :enddate, ActiveStatus = 0 WHERE ResidentID = :residentid";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':residentid', $residentID);
    $stmt->bindParam(':enddate', $returnDate);

    $stmt->execute();

    $newUserType = 6;

    $sql = "UPDATE member SET UserType = :usertype WHERE UserID = :userid";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':usertype', $newUserType);
    $stmt->bindParam(':userid', $userID);
    $stmt->execute();

    $sql = "SELECT RoomID FROM resident WHERE ResidentID = :residentid";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':residentid', $residentID);
    $stmt->execute();
    list($roomID) = $stmt->fetch(PDO::FETCH_NUM);

    $sql = "UPDATE room SET RoomStatus = 1 WHERE RoomID = :roomid";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':roomid', $roomID);
    $stmt->execute();

} catch (Exception $e) {
    echo "Error: {$e->getMessage()}";
    $db->rollback();
    die();
}

$db->commit();
header('Location: form_return_handle_admin.php');
exit();

