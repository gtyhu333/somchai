<?php

require 'DBconnect.php';

$stmt = $db->prepare("SELECT * FROM return_form WHERE ReturnID = ? LIMIT 1");
$stmt->bindParam(1, $_GET['id'], PDO::PARAM_INT);
$stmt->execute();
$stmt->setFetchMode(PDO::FETCH_ASSOC);
$result = $stmt->fetch();

?>

<div class="form-group" style="margin-bottom: 2rem">
    <label for="newstatus">สถานะ</label>
    <select name="newstatus" class="form-control">
    <option value="1"<?= $result['Status'] == '1' ? ' selected' : ''?>>ยังไม่อนุมัติ</option>
    <option value="2"<?= $result['Status'] == '2' ? ' selected' : ''?>>อนุมัติ</option>
    <option value="3"<?= $result['Status'] == '3' ? ' selected' : ''?>>ยกเลิก</option>
  </select>
  <input type="hidden" name="returnid" value="<?= $result['ReturnID']?>">
</div>

<button type="submit" class="btn btn-primary">แก้ไข</button>
<button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
