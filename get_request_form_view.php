<?php
require 'DBConnect.php';

$sql = 'SELECT * FROM staff WHERE StaffID = ?';
$stmt = $db->prepare($sql);
$stmt->bindParam(1, $_GET['id'], PDO::PARAM_INT);
$stmt->execute();
$form = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="form-horizontal">
    <div class="form-group">
        <label class="col-sm-2 control-label">เลขปชช.</label>
        <div class="col-sm-10">
            <p class="form-control-static"><?= $form['people_id'] ?></p>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">ชื่อ</label>
        <div class="col-sm-10">
            <p class="form-control-static"><?= $form['PName'] ?> <?= $form['Name'] ?> <?= $form['Surname'] ?></p>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">ตำแหน่ง</label>
        <div class="col-sm-10">
            <p class="form-control-static"><?= positionName($form['PositionID'], $db) ?> (<?= personalType($form['PersonnelType'], $db) ?>) </p>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">สังกัด</label>
        <div class="col-sm-10">
            <p class="form-control-static"><?= deptName($form['FacID'], $form['DeptID'], $db) ?> <?= facName($form['FacID'], $db) ?></p>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">สถานะสมรส</label>
        <div class="col-sm-10">
            <p class="form-control-static"><?= maritalStatus($form['MaritalStatus']) ?></p>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">จำนวนบุตร</label>
        <div class="col-sm-10">
            <p class="form-control-static"><?= $form['ChildrenCount'] ?></p>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">วันที่ร้องขอ</label>
        <div class="col-sm-10">
            <p class="form-control-static"><?= sqlDateToThaiDate($form['RequestDate']) ?></p>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">ที่อยู่</label>
        <div class="col-sm-10">
            <p class="form-control-static">
                <?= $form['Address'] ?> ตำบล<?= districtName($form['DistrictID'], $db) ?>
                อำเภอ<?= cityName($form['CityID'], $db) ?> จังหวัด <?= provinceName($form['ProvinceID'], $db) ?>
            </p>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">ประเภทห้องที่ร้องขอ</label>
        <div class="col-sm-10">
            <p class="form-control-static">
                <?= getRoomTypeName($form['RoomtypeID'], $db) ?>
            </p>
        </div>
    </div>
</div>
