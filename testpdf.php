<?php
require 'dompdf/autoload.inc.php';
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
        วันที่ 7 เดือน สิงหาคม พ.ศ. 2560
    </p>

    <p style="font-size: 1.75rem; padding: 0 5px;">
        &nbsp;&nbsp;&nbsp;&nbsp;
        ข้าพเจ้า <b>นายนิพนธ์ พวงพิมพ์</b> 
        ที่อยู่ <b>88 ม.8</b> 
        ตำบล <b>โพธ์ใหญ่</b> 
        อำเภอ <b>วารินชำราบ</b> <br>
        จังหวัด <b>อุบลราชธานี</b> 
        โทร. <b>099-0673529</b>
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
            <tr>
                <td>
                    ค่าจ้างรถรับส่งนักกีฬาระหว่างสถานที่จัดงานเลี้ยง - ที่พัก
                </td>
                <td>
                    6,150
                </td>
            </tr>
            <tr>
                <td style="text-align: right">
                    <b>รวมเป็นเงิน</b>
                </td>
                <td>
                    <b>6,150</b>
                </td>
            </tr>
        </tbody>
    </table>

    <p style="text-align: right; font-size: 1.75rem; padding: 0 10px; margin-top: 3rem">
        <br><br>ลงชื่อ______________________ <br><br>
        (นายนิพนธ์ พวงพิมพ์) <br><br>
        วันที่ __________ เดือน __________ พ.ศ. __________
    </p>
</body>
</html>

<?php
$html = ob_get_clean();

$dompdf = new \Dompdf\Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream();
$dompdf->stream("dompdf_out.pdf", array("Attachment" => false));
?>
