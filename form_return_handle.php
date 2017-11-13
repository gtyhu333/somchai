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

    $returnFormID = $db->lastInsertId();

// Add form for flat checker

    $sql = "INSERT INTO `return_form_checker` (`return_form_id`) 
    VALUES (:formid);";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':formid', $returnFormID);

    $stmt->execute();

// Add form for flat manager

    $sql = "INSERT INTO `return_form_manager` (`return_form_id`) 
    VALUES (:formid);";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':formid', $returnFormID);

    $stmt->execute();

} catch (Exception $e) {
    echo "Error: {$e->getMessage()}";
    $db->rollback();
    die();
}

$db->commit();

// dd($db->errorInfo());

header('Location: index_member.php');
exit();
