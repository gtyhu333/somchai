<?php

require 'DBConnect.php';

// dd($_POST);

$returnDate = implode('-', array_take($_POST, ['year', 'month', 'day']));

$db->beginTransaction();
try {
    $sql = "INSERT INTO `return_form` (`ResidentID`, `ReturnDate`, `Cause`) 
    VALUES (:residentid, :returndate , :cause);";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':residentid', $_POST['residentid']);
    $stmt->bindParam(':returndate', $returnDate);
    $stmt->bindParam(':cause', $_POST['cause']);

    $stmt->execute();
} catch (Exception $e) {
    echo "Error: {$e->getMessage()}";
    $db->rollback();
    die();
}

// try {
//     $sql = "UPDATE `resident` SET `EndDate` = :enddate WHERE `ResidentID` = :residentid;";
//     $stmt = $db->prepare($sql);

//     $stmt->bindParam(':enddate', $returnDate);
//     $stmt->bindParam(':residentid', $_POST['residentid']);

//     $stmt->execute();
// } catch (Exception $e) {
//     echo "Error: {$e->getMessage()}";
//     $db->rollback();
//     die();
// }

$db->commit();

header('Location: index_member.php');
exit();
