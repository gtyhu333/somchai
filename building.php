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

if ($_SESSION['user_type'] == 9  || $_SESSION['user_type'] == 5) {
  $buildingID = isset($_GET['building']) ? $_GET['building'] : 1;
} else {
  $buildingID = $_SESSION['building_id'];
}

try {
  $stmt = $db->prepare($sql = "
  SELECT building.BuildingName, room.RoomID, room.Floor, room.RoomName, allvars.ValueT as RoomStatus, room.RoomType,
  room.RoomRate, room.InsurantRate

  FROM building, room, allvars

  WHERE room.BuildingID = building.BuildingID
  AND allvars.FieldName = 'RoomStatus'
  AND room.RoomStatus = allvars.FieldCode
  AND building.BuildingID = ?
  ");
  $stmt->bindParam(1, $buildingID, PDO::PARAM_INT);
  $stmt->execute();
  $stmt->setFetchMode(PDO::FETCH_ASSOC);
  $result = $stmt->fetchAll();
}
catch(PDOException $e) {
  echo "Error: " . $e->getMessage();
}

try {
  $stmt = $db->prepare("
    SELECT
    SUM(CASE WHEN RoomStatus IN (1, 2) THEN 1 ELSE 0 END) Vacant,
    SUM(CASE WHEN RoomStatus IN (3) THEN 1 ELSE 0 END) Occupied,
    SUM(CASE WHEN RoomType IN (1, 4) THEN 1 ELSE 0 END) SingleRoom,
    SUM(CASE WHEN RoomType IN (2, 3, 5) THEN 1 ELSE 0 END) FamilyRoom
    FROM room
    WHERE BuildingID = ?;"
  );
  $stmt->bindParam(1, $buildingID, PDO::PARAM_INT);
  $stmt->execute();
  $stmt->setFetchMode(PDO::FETCH_ASSOC);
  $roomCount = $stmt->fetch();
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
                    <div class="col-lg-12" style="padding: 30px">
                        <div class="">
                            <div class="">
                                <div>
                                            <div class="form-group">
                                          <div class="col-lg-12">
                                        <hr>
                                        <font color ="#0080ff"><h2 class="intro-text text-center">การจัดการอาคารที่พัก</h2></font>
                                        <hr>
                                         </div>
                                            </div>
                                          </div>
                                          <?php if ($_SESSION['user_type'] == 9 || $_SESSION['user_type'] == 5): ?>
                                          <label>ชื่ออาคาร</label>
                                              <div class="row">
                                                <div class="col-md-6">
                                                  <select class="form-control" id="selectid" onchange="changevalue(this.value);">
                                                      <?php foreach ($resultBuilding as $value): ?>
                                                        <option value="<?= $value['BuildingID'] ?>" <?= $value['BuildingID'] == $buildingID ? ' selected' : '' ?>><?= $value['BuildingName'] ?></option>
                                                      <?php endforeach; ?>
                                                  </select>

                                                </div>

                                                <?php if ($_SESSION['user_type'] == 9): ?>
                                                  
                                                <div class="col-md-6">
                                                  <form action="building_update_form.php" method="GET" style="display: inline-block;">
                                                    <input type="hidden" name="id" value="<?= $buildingID ?>">
                                                    <button class="btn btn-primary" type="submit">แก้ไขอาคาร</button>
                                                  </form>

                                                  <form action="building_insert_form.php" method="GET" style="display: inline-block;">
                                                    <input type="hidden" name="id" value="1">
                                                    <button class="btn btn-primary" type="submit">เพิ่มอาคาร</button>
                                                  </form>

                                                  <form action="building_delete.php" method="POST" style="display: inline-block;"  onsubmit="return confirm('ต้องการลบอาคารหรือไม่')">
                                                    <input type="hidden" name="id" value="1">
                                                    <button class="btn btn-danger" type="submit">ลบอาคาร</button>
                                                  </form>


                                                <form action="room_insert_form.php" method="POST" style="display: inline-block;">
                                                  <input type="hidden" name="id" value="1">
                                                  <button class="btn btn-info" type="submit">เพิ่มห้องพัก</button>
                                                </form>
                                              <?php endif ?>

                                        </div>
                                        <?php endif ?>
                                        <div class="panel-body">
                                          <div class="row">
                                            <div class="col-md-6">
                                            <table class="table">
                                              <thead><th colspan="4">สรุปสถานะอาคาร</th></thead>
                                              <thead>
                                                <th>จำนวนห้องโสด</th>
                                                <th>จำนวนห้องครอบครัว</th>
                                                <th>ห้องพักว่าง</th>
                                                <th>ห้องพักที่มีผู้อยู่อาศัย</th>
                                              </thead>
                                              <tbody>
                                                <tr>
                                                  <td><?= $roomCount['SingleRoom'] ?> ห้อง</td>
                                                  <td><?= $roomCount['FamilyRoom'] ?> ห้อง</td>
                                                  <td><?= $roomCount['Vacant'] ?> ห้อง</td>
                                                  <td><?= $roomCount['Occupied'] ?> ห้อง</td>
                                                </tr>
                                              </tbody>
                                            </table>
                                          </div>
                                          </div>
                                            <div class="row">
                                              <div class="table-responsive">
                                                <table class="table table-bordered table-hover table-striped" id="room">
                                                    <thead>
                                                      <th><center>แฟลต</center></th>
                                                      <th><center>เลขห้อง</center></th>
                                                      <th><center>ชั้น</center></th>
                                                      <th><center>ประเภทห้อง</center></th>
                                                      <th><center>สถานะห้องพัก</center></th>
                                                      <th><center>ค่าเช่าห้องพัก</center></th>
                                                      <th><center>ค่าประกันของเสียหาย</center></th>
                                                      <th><center>ประวัติการเข้าพัก</center></th>
                                                      <?php if ($_SESSION['user_type'] == 9): ?>      
                                                      <th><center>แก้ไขข้อมูล</center></th>
                                                      <th><center>ลบข้อมูล</center></th>
                                                      <?php endif ?>
                                                    </thead>
                                                      <tbody>
                                                      <?php foreach ($result as $value) : ?>
                                                        <tr>
                                                          <td><center><?= $value['BuildingName'] ?></center></td>
                                                          <td><center><?= $value['RoomName'] ?></center></td>
                                                          <td><center><?= $value['Floor'] ?></center></td>
                                                          <td><center>
                                                            <?php
                                                            switch ($value['RoomType']) {
                                                              case 1:
                                                                echo 'ห้องโสด';
                                                                break;
                                                              case 2:
                                                                  echo 'ห้องครอบครัว';
                                                                  break;
                                                              case 3:
                                                                  echo 'เรือนรับรอง';
                                                                  break;
                                                              case 4:
                                                                  echo 'ห้องโสด';
                                                                  break;
                                                              case 5:
                                                                  echo 'ห้องครอบครัว';
                                                                  break;
                                                              case 6:
                                                                  echo 'เรือนรับรอง';
                                                                  break;
                                                            }
                                                            ?>
                                                          </center></td>
                                                          <td class="<?= getBuildingTableClass($value['RoomStatus']) ?>"><center><?= $value['RoomStatus'] ?></center></td>
                                                          <td><center><?= number_format($value['RoomRate']) ?></center></td>
                                                          <td><center><?= number_format($value['InsurantRate']) ?></center></td>
                                                          <td><center><button type="button" class="btn btn-default" onclick="openHistoryModal(<?= $value['RoomID'] ?>)">ประวัติ</button></center></td>
                                                          <?php if ($_SESSION['user_type'] == 9): ?>      
                                                          <td><center>
                                                              <a href="room_update_form.php?id=<?= $value['RoomID'] ?>" class="btn btn-primary" name="a">แก้ไข</a>
                                                          </center></td>
                                                        <td>  <center>
                                                            <form action="room_delete.php" method="post">
                                                              <input type="hidden" name="id" value="<?= $value['RoomID']?>">
                                                              <button type="submit" class="btn btn-danger" name="button">ลบ</button>
                                                            </form>
                                                          </certer></td>
                                                        <?php endif ?>
                                                        </tr>
                                                      <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            </div>
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

    <div class="modal fade" tabindex="-1" role="dialog" id="historymodal">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">ประวัติการเข้าพักอาศัยของห้อง</h4>
          </div>
          <div class="modal-body">
            <div class="historymodal-content">
      
            </div>
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

    <form action="building.php" method="get" id="redirectform">
      <input type="hidden" name="building" value="">
    </form>

    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

    <script type="text/javascript">
    $(document).ready(function() {
        $('input[name=id]').val($('#selectid').val());
    } );

    function changevalue(value) {
      $('input[name=building]').val($('#selectid').val());
      $('#redirectform').submit();
    }

    function openHistoryModal(id) {
      $.get('get_room_history.php?id=' + id, function(res) {
        $('.historymodal-content').html('');
        $('.historymodal-content').html(res);

        $('#historymodal').modal('show');
      });
    }
    </script>

</body>

</html>
