<?php

require 'DBConnect.php';

session_start();
if (!isset($_SESSION["user_id"])){
    header('Location: login.php');
    die();
}

$db->beginTransaction();

try {
    $sql = "UPDATE `member` SET 
    `UserPNameT` = :userpnamet, 
    `UserNameT` = :usernamet, 
    `UserSNameT` = :usersnamet,
    `Email` = :email, 
    `Phone` = :phone
    WHERE `UserID` = :userid;";

    $stmt = $db->prepare($sql);

    $stmt->bindParam(':userid', $_POST['userid']);
    $stmt->bindParam(':userpnamet', trim($_POST['userpnamet']));
    $stmt->bindParam(':usernamet', trim($_POST['usernamet']));
    $stmt->bindParam(':usersnamet', trim($_POST['usersnamet']));
    $stmt->bindParam(':email', trim($_POST['email']));
    $stmt->bindParam(':phone', trim($_POST['phone']));

    $stmt->execute();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    $db->rollback();
    die();
}

// Address
try {
    $stmt = $db->prepare("SELECT * FROM addresses WHERE UserID = :userid LIMIT 1");
    $stmt->bindParam(':userid', $_POST['userid']);
    $stmt->execute();
    $address = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$address) {
        $stmt = $db->prepare("
            INSERT INTO addresses (UserID, Address, CityID, DistrictID, ProvinceID, Zip) 
            VALUES (:userid, :address, :cityid, :districtid, :provinceid, :zip)
        ");

        $address = htmlspecialchars(trim($_POST['address']));

        $stmt->bindParam(':userid', $_POST['userid']);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':cityid', $_POST['city']);
        $stmt->bindParam(':districtid', $_POST['district']);
        $stmt->bindParam(':provinceid', $_POST['province']);
        $stmt->bindParam(':zip', $_POST['zip']);
        $stmt->execute();

    } else {
        $stmt = $db->prepare("
            UPDATE addresses SET Address = :address, CityID = :cityid, DistrictID = :districtid, 
            ProvinceID = :provinceid, zip = :zip WHERE UserID = :userid
        ");

        $address = htmlspecialchars(trim($_POST['address']));

        $stmt->bindParam(':userid', $_POST['userid']);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':cityid', $_POST['city']);
        $stmt->bindParam(':districtid', $_POST['district']);
        $stmt->bindParam(':provinceid', $_POST['province']);
        $stmt->bindParam(':zip', $_POST['zip']);
        $stmt->execute();
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    $db->rollback();
    die();
}

$db->commit();

header('Location: edit_profile.php');
exit();
