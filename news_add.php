<?php

require 'DBConnect.php';

$title = trim(htmlspecialchars($_POST['title']));
$body = trim(htmlspecialchars($_POST['body']));
$time = date("Y-m-d H:i:s");

try {
    $sql = "INSERT INTO news (title, content, date) VALUES (:title, :content, :date);";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':content', $body);
    $stmt->bindParam(':date', $time);
    $stmt->execute();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    die();
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();


