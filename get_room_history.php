<?php

require 'DBconnect.php';

$sql = "SELECT member.UserNameT, member.UserSNameT, resident.StartDate, resident.EndDate FROM member
RIGHT JOIN resident ON member.UserID = resident.UserID
WHERE resident.RoomID = ?";

$stmt = $db->prepare($sql);
$stmt->bindParam(1, $_GET['id'], PDO::PARAM_INT);
$stmt->execute();
$stmt->setFetchMode(PDO::FETCH_ASSOC);
$results = $stmt->fetchAll();

// dd($_GET['id']);

?>

<table class="table table-hover">
    <thead>
        <tr>
            <th>ชื่อ - สกุล</th>
            <th>วันที่เข้าพัก</th>
            <th>วันที่ออก</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($results as $result): ?>
            <tr>
                <td><?= $result['UserNameT'] ?> <?= $result['UserSNameT'] ?></td>
                <td><?= sqlDateToThaiDate($result['StartDate']) ?></td>
                <td>
                    <?php if ($result['EndDate'] == '0000-00-00'): ?>
                        -
                    <?php else: ?>
                        <?= sqlDateToThaiDate($result['EndDate']) ?>
                    <?php endif ?>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>
