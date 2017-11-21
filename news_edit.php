<?php

require 'DBConnect.php';

$id = $_POST['id'];
$title = trim(htmlspecialchars($_POST['title']));
$body = trim(htmlspecialchars($_POST['body']));

try {
    $sql = "UPDATE news SET title = :title, content = :content WHERE id = :id;";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':content', $body);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    die();
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
