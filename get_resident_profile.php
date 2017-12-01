<?php

require 'DBconnect.php';

try {
  $stmt = $db->prepare("SELECT * FROM v_resident WHERE `ResidentID` = ?;");
  $stmt->bindParam(1, $_GET['id']);
  $stmt->execute();
  $resident = $stmt->fetch(PDO::FETCH_ASSOC);

  $stmt = $db->prepare("SELECT * FROM addresses WHERE `UserID` = ?;");
  $stmt->bindParam(1, $resident['UserID']);
  $stmt->execute();
  $address = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (Exception $e) {
  echo "Error: {$e->getMessage()}";
  die();
}

$picPath = !file_exists(__DIR__ . '/userpic/' . $resident['UserID'] . '.jpg') ? null : 'userpic/' . $resident['UserID'] . '.jpg';
?>

<?php if ($picPath): ?>
  <div class="form-group">
    <label>รูปปัจจุบัน</label> <br>
    <img src="<?= $picPath ?>">
  </div>
<?php else: ?>
  <div class="form-group">
    <label>ผู้พักอาศัยนี้ยังไม่ได้อัพโหลดรูป</label> <br>
  </div>
<?php endif ?>

<p>ชื่อ: <?= $resident['Name'] ?></p>
<p>ตำแหน่ง: <?= $resident['Postion'] ?></p>
<p>สังกัด: <?= $resident['Faculty'] ?></p>
<?php if ($address): ?>
<p>ที่อยู่: <?= $address['Address'] ?> ตำบล<?= districtName($address['DistrictID'], $db) ?> 
  อำเภอ<?= cityName($address['CityID'], $db) ?> จังหวัด<?= provinceName($address['ProvinceID'], $db) ?> <?= $address['Zip'] ?></p>
<?php endif ?>

<?php if (!$address): ?>
  <label>ผู้พักอาศัยนี้ยังไม่ได้เพิ่มที่อยู่</label>
<?php endif ?>
