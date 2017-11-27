<?php

require 'DBConnect.php';

$stmt = $db->prepare("SELECT `DefaultRate` FROM expensetype WHERE `ExpenseName` = ?");
$stmt->bindParam(1, $_GET['name'], PDO::PARAM_STR);
$stmt->execute();
$stmt->setFetchMode(PDO::FETCH_ASSOC);

if ($stmt->rowCount() > 0) {
    echo json_encode($stmt->fetch());
} else {
    echo json_encode(['DefaultRate' => '0']);
}
