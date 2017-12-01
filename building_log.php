<!DOCTYPE html>
<?php
require 'DBconnect.php';

session_start();
if (!isset($_SESSION["user_id"])){
  header('Location: login.php');
  die();
}

if (!in_array($_SESSION['user_type'], [2, 3, 4, 5, 9])) {
  header('Location: login.php');
  die();
}

if ($_SESSION['user_type'] == 9 || $_SESSION['user_type'] == 5) {
  $buildingID = isset($_GET['building']) ? $_GET['building'] : 1;
} else {
  $buildingID = $_SESSION['building_id'];
}

$year = date('Y');
$month = date('n');

$buildingName = getBuidlingName($buildingID, $db);

try {
  $stmt = $db->prepare("SELECT 
    v_room.RoomName, 
    v_room.RoomType, 
    v_resident.Name, 
    v_resident.ResidentID, 
    v_resident.Postion, 
    v_resident.Faculty,
    v_resident.StartDate,
    v_resident.Status,
    (CASE 
    WHEN  v_resident.Status = '1'
    THEN
    CONCAT(
    YEAR(FROM_DAYS(DATEDIFF(now(), v_resident.StartDate))),'-',
    MONTH(FROM_DAYS(DATEDIFF(now(), v_resident.StartDate))),'-',
    DAY(FROM_DAYS(DATEDIFF(now(), v_resident.StartDate)))
    )
    END) As days
    FROM v_room
    LEFT JOIN v_resident ON v_resident.RoomID = v_room.RoomID AND v_resident.Status = '1'
    WHERE v_room.BuildingName = ?
    ");
  $stmt->bindParam(1, $buildingName);
  $stmt->execute();
  $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $stmt = $db->prepare("
    SELECT SUM(Electric) as electric, SUM(Water) as water, SUM(Room) as room, SUM(Sum) as sum 
    FROM payments 
    WHERE BuildingID = :buildingid AND Month = :month AND Year = :year
    ");

  $stmt->bindParam(':buildingid', $buildingID);
  $stmt->bindParam(':month', $month);
  $stmt->bindParam(':year', $year);
  $stmt->execute();

  $payments = $stmt->fetch(PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo "Error: " . $e->getMessage();
}

try {
  $stmt = $db->prepare("SELECT * FROM building;");
  $stmt->execute();
  $stmt->setFetchMode(PDO::FETCH_ASSOC);
  $resultBuilding = $stmt->fetchAll();
}
catch(PDOException $e) {
  echo "Error: " . $e->getMessage();
}
?>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>จัดการอาคาร</title>

  <!-- Bootstrap Core CSS -->
  <link href="css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom CSS -->
  <link href="css/business-casual.css?1" rel="stylesheet">

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

      <div class="brand">
        <div style="display: flex; justify-content: center; margin-top: 2px;">
          <img src="logo.png" height="75">
        </div>
        <h3>หอพักบุคลากรมหาวิทยาลัยอุบลราชธานี</h3></div>

        <!-- Navigation -->
        <?php if ($_SESSION['user_type'] == 2 || $_SESSION['user_type'] == 3): ?>
          <?php require 'user_2_nav.php'; ?>
        <?php else: ?>
          <?php require 'user_'. $_SESSION['user_type'] .'_nav.php'; ?>
        <?php endif ?>

        <div class="container">

          <div class="row">
            <div class="box" style="background-color: #fff">
              <div class="row">
                <div class="col-lg-12">
                  <div class="">
                    <div class="">
                      <div>
                        <div class="form-group">
                          <div class="col-lg-12">
                            <hr>
                            <font color ="#0080ff"><h2 class="intro-text text-center">
                              ความเคลื่อนไหวของ<?= getBuidlingName($buildingID, $db) ?>  
                            </h2>
                          </font>
                          <hr>
                        </div>
                      </div>
                    </div>
                    <?php if ($_SESSION['user_type'] == 9 || $_SESSION['user_type'] == 5): ?>
                      <div class="row" style="margin: 0">
                        <div class="col-md-6">
                          <label>ชื่ออาคาร</label>
                          <select class="form-control" id="selectid" onchange="changevalue(this.value);">
                            <?php foreach ($resultBuilding as $value): ?>
                              <option value="<?= $value['BuildingID'] ?>" <?= $value['BuildingID'] == $buildingID ? ' selected' : '' ?>><?= $value['BuildingName'] ?></option>
                            <?php endforeach; ?>
                          </select>
                        </div>
                      </div>
                    <?php endif ?>
                    <div class="panel-body">
                      <div class="row">
                        <div class="col-sm-12">
                          <table class="table table-bordered" id="logs">
                            <thead>
                              <tr>
                                <th>ห้อง</th>
                                <th>ผู้อยู่อาศัย</th>
                                <th>ตำแหน่ง</th>
                                <th>สังกัด</th>
                                <th>วันที่เข้าพัก</th>
                                <th>พักอาศัย <br> (ปี - เดือน - วัน)</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php foreach ($logs as $log): ?>
                                <tr>
                                  <td><?= $log['RoomName'] ?></td>
                                  <td>
                                    <?php if ($log['Name']): ?>
                                      <a href="#" onclick="getProfile(event, <?= $log['ResidentID'] ?>)">
                                        <?= $log['Name'] ?>
                                      </a>
                                    <?php else: ?>
                                      ว่าง
                                    <?php endif ?>
                                  </td>
                                  <td><?= $log['Postion'] ?  $log['Postion'] : 'ว่าง'?></td>
                                  <td><?= $log['Faculty'] ?  $log['Faculty'] : 'ว่าง'?></td>
                                  <td><?= $log['StartDate'] ? sqlDateToThaiDate($log['StartDate']) : 'ว่าง' ?></td>
                                  <td><?= $log['days'] ?  $log['days'] : 'ว่าง'?></td>
                                </tr>
                              <?php endforeach ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
<!-- 
                        <div class="row">
                          <div class="col-lg-12">
                            <hr>
                            <font color ="#0080ff"><h2 class="intro-text text-center">สรุปการเงินของหอพักประจำเดือน</h2></font>
                            <hr>
                          </div>
                        </div>

                        <div class="row">
                          <div class="col-lg-12">
                            <table class="table table-bordered">
                              <thead>
                                <tr>
                                  <th>ค่าไฟ (รวม)</th>
                                  <th>ค่าน้ำ (รวม)</th>
                                  <th>ค่าห้อง (รวม)</th>
                                  <th>รวมรายได้ประจำเดือน</th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td><?= $payments['electric'] ?> บาท</td>
                                  <td><?= $payments['water'] ?> บาท</td>
                                  <td><?= $payments['room'] ?> บาท</td>
                                  <td><?= $payments['sum'] ?> บาท</td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div> -->
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="clearfix"></div>
          </div>
        </div>
      </div>
      <!-- /.container -->

      <footer>
        <div class="container">
          <div class="row">
            <div class="col-lg-12 text-center">
              <p>Development by Thanut Pratumchat.</p>
            </div>
          </div>
        </div>
      </footer>

      <form action="building_log.php" method="get" id="redirectform">
        <input type="hidden" name="building" value="">
      </form>

      <div id="profileModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">ข้อมูลส่วนตัวของผู้พักอาศัย</h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
            </div>
          </div>

        </div>
      </div>

      <!-- jQuery -->
      <script src="js/jquery.js"></script>

      <!-- Bootstrap Core JavaScript -->
      <script src="js/bootstrap.min.js"></script>

      <script src="js/jquery.dataTables.min.js"></script>
      <script src="js/dataTables.bootstrap.min.js"></script>

      <script type="text/javascript">
        $(document).ready(function() {
          $('#logs').DataTable();
          $('input[name=id]').val($('#selectid').val());
        } );

        function changevalue(value) {
          $('input[name=building]').val($('#selectid').val());
          $('#redirectform').submit();
        }

        function getProfile(event, id) {
          event.preventDefault();

          var url = "get_resident_profile.php?id=" + id;

          $.get(url, function(response) {
            $('#profileModal .modal-body').html();
            $('#profileModal .modal-body').html(response);

            $('#profileModal').modal('show');
          });
        }
      </script>

    </body>

    </html>
