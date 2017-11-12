<?php

require 'DBconnect.php';

$stmt = $db->prepare("SELECT * FROM room WHERE BuildingID = ? ");
$stmt->bindParam(1, $_GET['id'], PDO::PARAM_INT);
$stmt->execute();
$stmt->setFetchMode(PDO::FETCH_ASSOC);
$result = $stmt->fetchAll();
?>

<?php if (empty($result)): ?>
    <option value="">-- โปรดเลือกแฟลต --</option>
<?php else: ?>

<?php
$rooms = [];

foreach ($result as $index => $item) {
    $rooms[$item['Floor']][$index] = $item;
}
?>

<?php foreach ($rooms as $floor => $rooms): ?>
    <optgroup label="ชั้นที่ <?= $floor ?>">
        <?php foreach ($rooms as $room): ?>
            <option value="<?=$room['RoomID']?>"><?=$room['RoomName']?></option>
        <?php endforeach ?>
    </optgroup>
<?php endforeach; ?>

<?php endif ?>
