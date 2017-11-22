<!DOCTYPE html>
<?php
require 'DBconnect.php';
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

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
$selectedMonth = isset($_GET['month']) ? $_GET['month'] : 1;
$selectedYear = isset($_GET['year']) ? $_GET['year'] : 2016;

try {
  $sql = "SELECT * FROM v_resident WHERE Name IS NOT NULL";
  $sql .= " AND BuildingID = {$buildingid}";
  $stmt = $db->prepare($sql);
  $stmt->execute();
  $stmt->setFetchMode(PDO::FETCH_ASSOC);
  $residents = $stmt->fetchAll();
} catch(PDOException $e) {
  echo "Error: " . $e->getMessage();
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
  return $bill['BasePrice'] + $bill['FT'] + $bill['ServiceCharge'] + $bill['Vat'];
}

function getBill($userid, $year, $month, $db)
{
  try {
      $stmt = $db->prepare("
        SELECT * FROM v_resident WHERE UserID = :userid AND Status = '1'"
      );

      $stmt->bindParam(':userid', $userid);
      $stmt->execute();
      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      $stmt = $db->prepare("
          SELECT 1 FROM payments WHERE ResidentID = :residentid AND Month = :month 
          AND Year = :year
      ");
      $thisMonth = $month;
      $thisYear = $year;
      $stmt->bindParam(':residentid', $user['ResidentID']);
      $stmt->bindParam(':month', $thisMonth);
      $stmt->bindParam(':year', $thisYear);
      $stmt->execute();
      $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $stmt->closeCursor();
      
      if ($needPayment = empty($payments)) {
          $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, true); // fixes 'packet ouf of order bug'
          $stmt = $db->prepare("CALL getBill(:roomid, :year, :month)");
          $stmt->bindParam(':roomid', $user['RoomID']);
          $stmt->bindParam(':year', $year);
          $stmt->bindParam(':month', $month);
          $stmt->execute();
          $bills = $stmt->fetch(PDO::FETCH_ASSOC);
          $stmt->closeCursor();

          if (empty($bills)) {
            return null;
          }

          array_walk($bills, function (&$value, $key) {
              return [$key => number_format($value)];
          });

          return $bills;
      } else {
        return null;
      }

  } catch (Exception $e) {
      echo "Error {$e->getMessage()}";
      echo "{$e->getTraceAsString()}";
      die();
  }
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
              <h2 class="intro-text text-center" style="color: #0080ff">
                การจัดการยอดค้างชำระ
              </h2>
            <hr>

            <div class="row" style="margin-top: 16px;">
              <div class="col-md-4">
                <label style="font-size: 1.5rem">ค้นหาบิลค่าห้อง:</label>
                <form action="resident_monthly_expense.php" method="get" id="buildingform">

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
              <div class="col-md-12">
                <table class="table table-bordered table-stripped table-hover" id="repairtable">
                  <thead>
                    <th>ห้อง</th>
                    <th>ชื่อ - สกุล</th>
                    <th>ค่าน้ำ</th>
                    <th>ค่าไฟ</th>
                    <th>ค่าห้อง</th>
                    <th>รวมทั้งสิ้น (บาท)</th>
                    <th>ตัวเลือก</th>
                  </thead>
                  <tbody>
                    <?php foreach ($residents as $resident): ?>
                    <?php 
                      $bills = getBill(
                        $resident['UserID'], $selectedYear, $selectedMonth, $db
                      );
                    ?>
                    <tr>
                      <td><?= roomNumber($resident['RoomID'], $db) ?></td>
                      <td><?= $resident['Name'] ?></td>
                      <td><?= $bills ? $bills['water_total'] : '-' ?></td>
                      <td><?= $bills ? $bills['electric_total'] : '-' ?></td>
                      <td><?= $bills ? $bills['room_price'] : '-' ?></td>
                      <td><?= $bills ? $bills['total'] : '-' ?></td>
                      <td>
                        <form action="add_payment.php" method="POST">
                          <input type="hidden" name="residentid" value="<?= $resident['ResidentID'] ?>">
                          <input type="hidden" name="month" value="<?= $selectedMonth ?>">
                          <input type="hidden" name="year" value="<?= $selectedYear ?>">
                          <input type="hidden" name="water" value="<?= $bills['water_total'] ?>">
                          <input type="hidden" name="electric" value="<?= $bills['electric_total'] ?>">
                          <input type="hidden" name="room" value="<?= $bills['room_price'] ?>">
                          <input type="hidden" name="sum" value="<?= $bills['total'] ?>">
                          <input type="hidden" name="buildingid" value="<?= $buildingid ?>">
                          <button type="submit" class="btn btn-primary"
                          <?= $bills ? '' : ' disabled' ?>>จ่ายแล้ว</button>
                        </form>
                      </td>
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
            <h4 class="modal-title">อัพโหลดค่าไฟสำหรับอาคารนี้</h4>
          </div>
          <div class="modal-body">
            <form action="add_electric_bills.php" method="post" enctype="multipart/form-data">
              <div class="form-group">
                <label>ไฟล์ค่าไฟ (.csv)</label>
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
