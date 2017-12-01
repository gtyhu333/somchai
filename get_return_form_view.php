<?php

require 'DBconnect.php';

$sql = "
  SELECT `return_form`.`ReturnID`,`v_resident`.`Name`, `v_resident`.`Building`,`v_resident`.`BuildingID`,
  `v_resident`.`RoomID`,`return_form`.`ReturnDate`,`return_form`.`Cause`,`return_form`.`Status`, 
  `return_form_checker`.`submitted` as `checker_submitted`, `return_form_manager`.`submitted` as `manager_submitted`
  FROM return_form
  INNER JOIN `v_resident` ON `return_form`.`ResidentID` = `v_resident`.`ResidentID` 
  INNER JOIN `return_form_checker` ON `return_form`.`ReturnID` = `return_form_checker`.`return_form_id` 
  INNER JOIN `return_form_manager` ON `return_form`.`ReturnID` = `return_form_manager`.`return_form_id` 
  WHERE `return_form`.`ReturnID` = ? AND `return_form`.`Status` != 3 LIMIT 1";

$stmt = $db->prepare($sql);
$stmt->bindParam(1, $_GET['id'], PDO::PARAM_INT);
$stmt->execute();
$stmt->setFetchMode(PDO::FETCH_ASSOC);
$result = $stmt->fetch();

?>

<div class="form-group" style="margin-bottom: 2rem">
    <label>ส่วน : ผู้พักอาศัย </label>
    <div class="form-horizontal">
        <div class="form-group">
            <label class="col-sm-3 control-label">ชื่อ-สกุล</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" disabled value="<?= $result['Name'] ?>">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">ห้อง</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" disabled value="<?= roomNumber($result['RoomID'], $db) ?>">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">วันที่ขอคืนห้อง</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" disabled value="<?= sqlDateToThaiDate($result['ReturnDate']) ?>">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">สาเหตุ</label>
            <div class="col-sm-8">
                <textarea cols="30" rows="10" class="form-control" disabled><?= $result['Cause'] ?></textarea>
            </div>
        </div>
    </div>

    <hr>

    <?php require 'get_return_manager_form_view.php' ?>
    
    <hr>

    <?php require 'get_return_checker_form_view.php' ?>
</div>

<script>
$('#check_1').on('change', function () {
    if ($(this).is(':checked')) {
        $('#cost').attr("readonly", false);
    } else {
        $('#cost').attr("readonly", true);
    }
});
</script>

