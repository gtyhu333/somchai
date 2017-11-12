<?php

require 'DBConnect.php';

try {
    $sql = "DELETE FROM return_form WHERE ReturnID = :returnid";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':returnid', $_POST['id']);

    $stmt->execute();
} catch (Exception $e) {
    echo "Error: {$e->getMessage()}";
    die();
}

header('Location: form_return_handle_admin.php');
exit();
