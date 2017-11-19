<?php

require 'DBconnect.php';

try {
  $stmt = $db->prepare("SELECT * FROM room_stuff WHERE `RoomID` = ?;");
  $stmt->bindParam(1, $_GET['id']);
  $stmt->execute();
  $stuffs = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
  echo "Error: {$e->getMessage()}";
  die();
}
?>
<?php if (empty($stuffs)): ?>
<div class="row">
    <p style="font-size: 1em; color: #777">ห้องนี้ยังไม่มีครุภัณฑ์ 
        <a href="room_update_stuff_form.php?roomid=<?= $_GET['id'] ?>">คลิกเพื่อไปยังหน้าแก้ไขครุภัณฑ์</a>
    </p>
</div>
<?php else: ?>
<?php foreach (array_chunk($stuffs, 3) as $chunk): ?>
<div class="row">
<div class="form-group">
  <?php foreach ($chunk as $stuff): ?>     
  <label class="checkbox-inline">
    <input type="checkbox" name="item[]" value="<?= $stuff['Name'] ?>"> <?= $stuff['Name'] ?>
  </label>
  <?php endforeach ?>
</div>
</div>
<?php endforeach ?>
<?php endif ?>
