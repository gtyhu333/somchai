<?php

require 'DBconnect.php';

$stmt = $db->prepare("SELECT * FROM score WHERE StaffID = ? ORDER BY EvaluateDate DESC;");
$stmt->bindParam(1, $_GET['id'], PDO::PARAM_INT);
$stmt->execute();
$stmt->setFetchMode(PDO::FETCH_ASSOC);
$scores = $stmt->fetchAll();
?>
<style>
.tab-pane {
    margin-top: 2em;
}

.tab-pane p {
    font-size: 1em;
}
</style>

<div>
    <ul class="nav nav-tabs" role="tablist">
        <li class="active">
            <a href="#summary" aria-controls="summary" data-toggle="tab">รายละเอียดคะแนนล่าสุด</a>
        </li>
        <li>
            <a href="#history" aria-controls="history" data-toggle="tab">ประวัติการประมวลผลคะแนน</a>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane active" id="summary">
            <p>คะแนนตำแหน่งบุคลากร: <?= $scores[0]['PositionScore'] ?></p>
            <p>คะแนนภูมิลำเนา: <?= $scores[0]['CityScore'] ?></p>
            <p>คะแนนประสบภัย: <?= $scores[0]['DisasterScore'] ?></p>
            <p>คะแนนสถานภาพ: <?= $scores[0]['MaritalScore'] ?></p>
            <p>คะแนนระยะเวลาปฏิบัติงาน: <?= $scores[0]['EmployScore'] ?></p>
            <p>วันที่ประมวลผลคะแนนล่าสุด: <?= sqlDateToThaiDate($scores[0]['EvaluateDate']) . ' ' . date('H:i', strtotime($scores[0]['EvaluateDate'])) ?></p>
        </div>

        <div class="tab-pane" id="history">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>วันที่ประเมิน</th>
                        <th>คะแนน</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($scores as $score): ?>
                    <tr>
                        <td><?= sqlDateToThaiDate($score['EvaluateDate']) . ' ' . date('H:i', strtotime($score['EvaluateDate'])) ?></td>
                        <td><?= $score['Score'] ?> <a data-toggle="collapse" href="#scoreDetail<?= $score['ScoreID'] ?>">ดูรายละเอียด</a></td>
                    </tr>
                    <tr class="collapse" id="scoreDetail<?= $score['ScoreID'] ?>" style="background-color: #f7f7f7">
                        <td colspan="2">
                            <p>คะแนนตำแหน่งบุคลากร: <?= $score['PositionScore'] ?></p>
                            <p>คะแนนภูมิลำเนา: <?= $score['CityScore'] ?></p>
                            <p>คะแนนประสบภัย: <?= $score['DisasterScore'] ?></p>
                            <p>คะแนนสถานภาพ: <?= $score['MaritalScore'] ?></p>
                            <p>คะแนนระยะเวลาปฏิบัติงาน: <?= $score['EmployScore'] ?></p>
                            <p>วันที่ประมวลผลคะแนนล่าสุด: <?= sqlDateToThaiDate($score['EvaluateDate']) . ' ' . date('H:i', strtotime($score['EvaluateDate'])) ?></p>
                        </td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
