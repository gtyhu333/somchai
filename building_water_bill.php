<!DOCTYPE html>
<?php
require 'DBconnect.php';

$months = [
  "ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค."
];

session_start();
if (!isset($_SESSION["user_id"])){
  header('Location: login.php');
  die();
}

if ($_SESSION['user_type'] != 9) {
  header('Location: login.php');
  die();
}

$buildingid = isset($_GET['building']) ? $_GET['building'] : 1;

try {
  $sql = "SELECT * FROM water_bills WHERE BuildingID = {$buildingid}";

  if (isset($_GET['month'])) {
    $sql .= " AND MONTH(RecordDate) = {$_GET['month']}";
  } else {
    $sql .= " AND MONTH(RecordDate) = 1";
  }

  if (isset($_GET['year'])) {
    $sql .= " AND YEAR(RecordDate) = {$_GET['year']}";
  } else {
    $sql .= " AND YEAR(RecordDate) = 2016";
  }

  // dd($sql);

  $stmt = $db->prepare($sql);
  $stmt->execute();
  $electricBills = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
  echo "Error: {$e->getMessage()}";
  die();
}

try {
  $stmt = $db->prepare("SELECT * FROM building");
  $stmt->execute();
  $stmt->setFetchMode(PDO::FETCH_ASSOC);
  $buildings = $stmt->fetchAll();
} catch(PDOException $e) {
  echo "Error: " . $e->getMessage();
}

$selectedBuilding = array_filter($buildings, function ($building) use ($buildingid) {
  return $building['BuildingID'] == $buildingid;
});

$selectedBuilding = reset($selectedBuilding);

// dd($selectedBuilding);

function getResidentName($roomID, $db)
{
  $stmt = $db->prepare("SELECT Name FROM v_resident WHERE RoomID = :roomid LIMIT 1;");
  $stmt->bindParam(':roomid', $roomID);
  $stmt->execute();
  
  return ($stmt->fetch(PDO::FETCH_ASSOC))['Name'];
}

function sumBill($bill)
{
  return $bill['BasePrice'] + $bill['ServiceCharge'] + $bill['Vat'];
}

?>
}
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>จัดการแบบฟอร์มร้องขอ</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/business-casual.css" rel="stylesheet">

    <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Josefin+Slab:100,300,400,600,700,100italic,300italic,400italic,600italic,700italic" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>
  <div style="display: flex; justify-content: center; margin-top: 16px;">
    <img src="logo.png" height="100">
  </div>

  <div class="brand">หอพักบุคลากร</div>
  <div class="address-bar">มหาวิทยาลัยอุบลราชธานี</div>

<!-- Navigation -->
    <?php require 'admin_nav.php'; ?>

    <div class="container">
      <div class="box" style="background-color: #fff; padding: 30px;">
        <div class="">
          <div class="">
            <hr>
              <h2 class="intro-text text-center" style="color: #0080ff">การจัดการค่าน้ำ</h2>
            <hr>

            <div class="row" style="margin-top: 16px;">
              <div class="col-md-4">
                <label style="font-size: 1.5rem">ค้นหาบิลค่าน้ำ:</label>
                <form action="building_water_bill.php" method="get" id="buildingform">

                  <div class="form-group">
                    <label>ชื่ออาคาร</label>
                    <select class="form-control" name="building">
                      <?php foreach ($buildings as $building): ?>
                        <option value="<?= $building['BuildingID'] ?>"<?= isset($_GET['building']) &&  $_GET['building'] == $building['BuildingID'] ? ' selected' : '' ?>>
                          <?= $building['BuildingName'] ?>
                        </option>
                      <?php endforeach ?>
                    </select>
                  </div>
                  
                  <div class="form-group">
                    <label>เดือน</label>
                    <select class="form-control" name="month">
                      <?php foreach ($months as $key => $month): ?>
                        <option value="<?= $key + 1 ?>"<?= isset($_GET['month']) &&  $_GET['month'] == $key + 1 ? ' selected' : '' ?>>
                          <?= $month ?>
                        </option>
                      <?php endforeach ?>
                    </select>
                  </div>

                  <div class="form-group">
                    <label>ปี</label>
                    <select class="form-control" name="year">
                      <?php for ($year = 2016; $year <= date("Y"); $year++): ?>
                        <option value="<?= $year ?>"<?= isset($_GET['year']) &&  $_GET['year'] == $year ? ' selected' : '' ?>>
                          <?= $year + 543 ?>
                        </option>
                      <?php endfor ?>
                    </select>
                  </div>

                  <button class="btn btn-primary" type="submit">ค้นหา</button>
            
                </form>
              </div>
            </div>

            <div class="row" style="margin-top: 16px;">
              <div class="col-md-4">
                <button class="btn btn-default" type="button" data-toggle="modal" data-target="#fileModal">
                  เพิ่มค่าน้ำสำหรับ<?= $selectedBuilding['BuildingName'] ?>
                </button>
              </div>
            </div>
            

            <div class="row" style="margin-top: 16px;">
              <div class="col-md-12">
                <table class="table table-bordered table-stripped table-hover" id="repairtable">
                  <thead>
                    <th>ห้อง</th>
                    <!-- <th>ชื่อ - สกุล</th> -->
                    <!-- <th>Serial Number</th> -->
                    <th>หน่วยเดือนก่อน</th>
                    <th>หน่วยเดือนนี้</th>
                    <th>จำนวนหน่วย (Kw/h)</th>
                    <th>คิดเป็น (บาท)</th>
                    <th>ค่า บริการ (บาท)</th>
                    <th>VAT (บาท)</th>
                    <th>รวมทั้งสิ้น (บาท)</th>
                    <th>วันที่บันทึก</th>
                  </thead>
                  <tbody>
                    <?php foreach ($electricBills as $bill): ?>
                    <tr>
                      <td><?= roomNumber($bill['RoomID'], $db) ?></td>
                      <td><?= $bill['LastMonthCount'] ?></td>
                      <td><?= $bill['ThisMonthCount'] ?></td>
                      <td><?= ((int) $bill['ThisMonthCount']) - ((int) $bill['LastMonthCount']) ?></td>
                      <td><?= $bill['BasePrice'] ?></td>
                      <td><?= $bill['ServiceCharge'] ?></td>
                      <td><?= $bill['Vat'] ?></td>
                      <td><?= sumBill($bill) ?></td>
                      <td><?= sqlDateToThaiDate($bill['RecordDate']) ?></td>
                    </tr>
                    <?php endforeach ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /.container -->

    <div class="modal fade" tabindex="-1" role="dialog" id="fileModal">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">เพิ่มค่าน้ำสำหรับ<?= $selectedBuilding['BuildingName'] ?></h4>
          </div>
          <div class="modal-body">
            <form action="add_water_bills.php" method="post" enctype="multipart/form-data">
              <div class="form-group">
                <label>ไฟล์ค่าน้ำ (.csv)</label>
                <input type="hidden" name="buildingid" value="<?= isset($_GET['building']) ? $_GET['building'] : 1 ?>">
                <input type="file" name="file" accept=".csv" required>
              </div>

              <div class="form-group">
                <button type="submit" class="btn btn-primary">อัพโหลด</button>
              </div>

              <p><b>หมายเหตุ:</b> ให้ใช้ไฟล์ในรูปแบบี่กำหนดใน<a href="#">ไฟล์นี้</a>เท่านั้น</p>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div>

    <footer>
      <div class="container">
        <div class="row">
          <div class="col-lg-12 text-center">
            <p>Development by Thanut Pratumchat.</p>
          </div>
        </div>
      </div>
    </footer>

    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>

    <script type="text/javascript">
    $(document).on('ready', function() {
      $('#repairtable').DataTable();
    });
    </script>

</body>

</html>
