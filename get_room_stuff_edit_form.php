<?php

require 'DBconnect.php';

$stmt = $db->prepare("SELECT * FROM room_stuff WHERE id = ? LIMIT 1");
$stmt->bindParam(1, $_GET['id'], PDO::PARAM_INT);
$stmt->execute();
$stmt->setFetchMode(PDO::FETCH_ASSOC);
$stuff = $stmt->fetch();

?>
<form action="room_update_stuff.php" method="post">
    <div class="form-group">
        <label>ชื่อครุภัณฑ์</label>
        <input type="text" name="name" class="form-control" value="<?= $stuff['Name'] ?>">
    </div>

    <input type="hidden" name="id" value="<?= $_GET['id'] ?>">
    <button type="submit" class="btn btn-primary">แก้ไข</button>
</form>
