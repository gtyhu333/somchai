<?php
require 'dompdf/autoload.inc.php';
require 'DBConnect.php';

ob_start(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Test PDF</title>
    <style>
        <?= file_get_contents('css/bootstrap.css') ?>
        @font-face {
            font-family: 'THSarabunPSK';
            font-style: normal;
            font-weight: normal;
            src: url("fonts/THSarabun.ttf") format('truetype');
        }

        @font-face {
            font-family: 'THSarabunPSK';
            font-style: normal;
            font-weight: bold;
            src: url("fonts/THSarabun Bold.ttf") format('truetype');
        }

        @font-face {
            font-family: 'THSarabunPSK';
            font-style: italic;
            font-weight: normal;
            src: url("fonts/THSarabun Italic.ttf") format('truetype');
        }

        @font-face {
            font-family: 'THSarabunPSK';
            font-style: italic;
            font-weight: bold;
            src: url("fonts/THSarabun Bold Italic.ttf") format('truetype');
        }
 
        body {
            font-family: "THSarabunPSK" !important;
        }

        p {
            line-height: 2.25rem;
        }

        tbody:before, tbody:after { display: none; }

/*        .table-bordered th,
        .table-bordered td {
            border: 1px solid #000 !important;
        }*/
    </style>
</head>
<body style="border: 1px solid #000">
    <h1 style="text-align: center">ใบสำคัญรับเงิน</h1>

    <p style="text-align: right; font-size: 1.75rem; padding: 0 5px;">
        วันที่ <?= $_POST['day'] ?> เดือน <?= $_POST['month'] ?> พ.ศ. <?= $_POST['year'] ?>
    </p>

    <p style="font-size: 1.75rem; padding: 0 5px;">
        &nbsp;&nbsp;&nbsp;&nbsp;
        ข้าพเจ้า <b><?= $_POST['receiptname'] ?></b> 
        ที่อยู่ <b><?= $_POST['street'] ?></b> 
        ตำบล <b><?= districtName($_POST['tambon'], $db) ?></b> 
        อำเภอ <b><?= cityName($_POST['amphoe'], $db) ?></b> <br>
        จังหวัด <b><?= provinceName($_POST['province'], $db) ?>, <?= $_POST['zip'] ?></b> 
        โทร. <b><?= $_POST['phone'] ?></b>
    </p>

    <p style="font-size: 1.75rem; padding: 0 5px;">
        <b>ได้รับเงินจากมหาวิทยาลัยอุบลราชธานี <br>
        ดังรายการต่อไปนี้</b>
    </p>

    <table class="table table-bordered" style="width: 100%; font-size: 1.5rem;">
        <thead>
            <tr>
                <th style="text-align: center;">รายการ</th>
                <th style="text-align: center;">จำนวนเงิน</th>
            </tr>
        </thead>
        <tbody>
                <?php foreach ($_POST['name'] as $index => $name): ?>
                <tr>
                    <td><?= $name ?></td>
                    <td><?= number_format($_POST['price'][$index]) ?></td>
                </tr>
                <?php endforeach ?>
            <tr>
                <td style="text-align: right">
                    <b>รวมเป็นเงิน</b>
                </td>
                <td>
                    <?=  
                    number_format(array_sum($_POST['price'])); 
                    ?>
                </td>
            </tr>
        </tbody>
    </table>

    <p style="text-align: right; font-size: 1.75rem; padding: 0 10px; margin-top: 3rem">
        <br><br>ลงชื่อ______________________ <br><br>
        (<?= $_POST['receiptname'] ?>)
    </p>
</body>
</html>

<?php
$html = ob_get_clean();

$dompdf = new \Dompdf\Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("expense_paper.pdf");
//$dompdf->stream("dompdf_out.pdf", array("Attachment" => false));
?>
