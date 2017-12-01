<?php
if (!isset($db)) {
    require 'DBconnect.php';
}

$sql = "SELECT * FROM `return_form_manager` INNER JOIN `member` 
ON `return_form_manager`.`submitted_user` = `member`.`UserID` 
WHERE `return_form_id` = ?";

$stmt = $db->prepare($sql);
$stmt->bindParam(1, $_GET['id'], PDO::PARAM_INT);
$stmt->execute();
$stmt->setFetchMode(PDO::FETCH_ASSOC);
$result = $stmt->fetch();

?>

<div class="form-group" style="margin-bottom: 2rem">
    <label>ส่วน : ความเห็นของประธานกรรมการประจำอาคาร</label>
    <form>
        <div class="checkbox">
            <label><input type="checkbox" name="check_1" disabled<?= $result['check_1'] == 1 ? ' checked' : '' ?>> ได้รับเงินสด / เช็คเพิ่มเติมจากผู้พักอาศัยแล้ว</label>
        </div>
        <div class="form-horizontal">
            <div class="form-group">
                <label class="col-sm-3 control-label" style="text-align: left; font-weight: normal;">เป็นจำนวนเงิน</label>
                <div class="col-sm-6 input-group">
                    <input type="number" class="form-control" name="cost" disabled value="<?= $result['cost'] ?>">
                    <div class="input-group-addon">บาท</div>
                </div>
            </div>
        </div>

        <div class="checkbox">
            <label><input type="checkbox" name="check_2" disabled<?= $result['check_2'] == 1 ? ' checked' : '' ?>> ได้รับกุญแจและคีย์การ์ดคืนแล้ว</label>
        </div>

        <div class="checkbox">
            <label><input type="checkbox" name="check_3" disabled<?= $result['check_3'] == 1 ? ' checked' : '' ?>> ที่พักอาศัยมีสภาพเรียบร้อยพร้อมพักอาศัย</label>
        </div>

        <div class="form-group">
            <label>ความเห็น</label>
            <textarea name="comment" cols="30" rows="5" class="form-control" disabled><?= $result['comment'] ?></textarea>
        </div>
        
        <div class="form-horizontal">
            
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
$('#check_1').on('change', function () {
    if ($(this).is(':checked')) {
        $('#cost').attr("readonly", false);
    } else {
        $('#cost').attr("readonly", true);
    }
});
</script>
