<?php

require 'DBconnect.php';

$sql = "SELECT * FROM `return_form_checker` INNER JOIN `member` 
ON `return_form_checker`.`submitted_user` = `member`.`UserID` 
WHERE `return_form_id` = ?";

$stmt = $db->prepare($sql);
$stmt->bindParam(1, $_GET['id'], PDO::PARAM_INT);
$stmt->execute();
$stmt->setFetchMode(PDO::FETCH_ASSOC);
$result = $stmt->fetch();

?>

<div class="form-group" style="margin-bottom: 2rem">
    <label>ส่วน : ความเห็นของอนุกรรมการประจำอาคาร</label>
    <form>
        <div class="checkbox">
            <label><input type="checkbox" disabled<?= $result['check_1'] == 1 ? ' checked' : '' ?>> สภาพห้องเรียบร้อย</label>
        </div>
        <div class="checkbox">
            <label><input type="checkbox" disabled<?= $result['check_2'] == 1 ? ' checked' : '' ?>> ครุภัณฑ์ครบถ้วนและใช้การได้ดี เห็นควรคืนค่าประกัน ค่าเสียหาย ค่ามัดจำ</label>
        </div>
        <div class="checkbox">
            <label><input type="checkbox" disabled<?= $result['check_3'] == 1 ? ' checked' : '' ?>> สภาพห้องไม่เรียบร้อย วัสดุ อุปกรณ์ครุภัณฑ์มีการชำรุด สึกหรอ จำเป็นต้องปรับปรุง ซ่อมแซม หรือซื้อทดแทนของเดิม ดังนี้</label>
        </div>

        <div class="form-group">
            <textarea cols="30" rows="5" class="form-control" disabled><?= $result['check_3_comment'] ?></textarea>
        </div>

        <div class="form-horizontal">
            <div class="form-group">
                <label class="col-sm-3 control-label" style="text-align: left">ประมาณรายจ่าย</label>
                <div class="col-sm-6 input-group">
                    <input type="number" class="form-control" disabled value="<?= $result['cost'] ?>">
                    <div class="input-group-addon">บาท</div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label" style="text-align: left">ผู้กรอกแบบฟอร์ม</label>
                <div class="col-sm-6" style="padding: 0">
                    <input type="text" class="form-control" disabled value="<?= $result['UserPNameT'] . ' ' . $result['UserNameT'] . ' ' . $result['UserSNameT'] ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label" style="text-align: left">วันที่กรอกแบบฟอร์ม</label>
                <div class="col-sm-6" style="padding: 0">
                    <input type="text" class="form-control" disabled value="<?= sqlDateToThaiDate($result['submitted_date']) ?>">
                </div>
            </div>
        </div>

    </form>
</div>

<script>
$('#check_3').on('change', function () {
    if ($(this).is(':checked')) {
        $('textarea[name="check_3_comment"]').attr("readonly", false);
    } else {
        $('textarea[name="check_3_comment"]').attr("readonly", true);
    }
});
</script>
