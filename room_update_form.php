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
try {
  $stmt = $db->prepare("SELECT * FROM v_room where RoomID = :id");
  $stmt->bindParam(':id',$_GET['id']);
  $stmt->execute();
  $stmt->setFetchMode(PDO::FETCH_ASSOC);
  $result = $stmt->fetch();
}
catch(PDOException $e) {
  echo "Error: " . $e->getMessage();
}

try {
  $stmt = $db->prepare("SELECT `BuildingID`, `BuildingName` FROM `building`");
  $stmt->execute();
  $stmt->setFetchMode(PDO::FETCH_ASSOC);
  $resultBuilding = $stmt->fetchAll();
} catch (Exception $e) {
  echo "Error: " . $e->getMessage();
}
$conn = null;

// die(var_dump($result));
 ?>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>แก้ไขห้องพัก</title>

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
            <div class="box">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="table-responsive">
                                  <div class="row">

                                    <div class="col-lg-12">
                                  <hr>
                                  <font color ="#0080ff"><h2 class="intro-text text-center">แก้ไขห้องพัก <?= $result['RoomName'] ?> <?= $result['BuildingName'] ?></h2></font>
                                  <hr>
                                   </div>
                                   <div class="col-lg-6">

                                    <form role="form" action= "room_update.php" method="post">

                                      <div class="form-group">
                                        <label>เลือกแฟลต</label>
                                        <select class="form-control" name="build1">
                                          <?php foreach ($resultBuilding as $building): ?>
                                            <option value="<?= $building['BuildingID'] ?>"<?= $result['BuildingName'] == $building['BuildingName'] ? ' selected' : '' ?>>
                                              <?= $building['BuildingName'] ?>
                                              </option>
                                          <?php endforeach; ?>
                                        </select>
                                      </div>

                                      <div class="form-group">
                                        <label>เพิ่มเลขห้อง</label>
                                        <input class="form-control" name="roomname1" value="<?= $result['RoomName']?>">
                                        <p class="help-block">ตัวอย่าง : 101 </p>
                                      </div>

                                      <div class="form-group">
                                        <label>กำหนดชั้น</label>
                                        <input class="form-control" name="floor1" value="<?= $result['Floor']?>">
                                        <p class="help-block">ตัวอย่าง : 1 </p>
                                      </div>

                                      <div class="form-group">
                                        <label>เลือกประเภทห้อง</label>
                                        <select class="form-control" name= "roomtype1" value="<?= $result['RoomType']?>">
                                          <option value="1"<?= $result['RoomType'] == 'ห้องโสด' ? ' selected' : ''?>>ห้องโสด</option>
                                          <option value="2"<?= $result['RoomType'] == 'ห้องครอบครัว' ? ' selected' : ''?>>ห้องครอบครัว</option>
                                          <option value="3"<?= $result['RoomType'] == 'เรือนรับรอง' ? ' selected' : ''?>>เรือนรับรอง</option>
                                        </select>
                                      </div>

                                      <div class="form-group">
                                        <label>เลือกสถานะห้องพัก</label>
                                        <select class="form-control" name="roomstatus1" value="<?= $result['RoomStatus']?>">
                                          <option value="1">ว่าง พร้อมใช้งาน</option>
                                          <option value="2"<?= $result['RoomStatus'] != 'ไม่ว่าง' ? ' selected' : '' ?>>ไม่ว่าง</option>
                                        </select>
                                      </div>

                                      <div class="form-group">
                                        <label>กำหนดค่าเช่าห้องพัก</label>
                                        <input class="form-control" name="roomrate1" value="<?= $result['RoomRate']?>">
                                        <p class="help-block">ตัวอย่าง : 1000 </p>
                                      </div>

                                      <div class="form-group">
                                        <label>กำหนดค่าของเสียหาย</label>
                                        <input class="form-control" name="insurate1" value="<?= $result['InsurantRate']?>">
                                        <p class="help-block">ตัวอย่าง : 1000 </p>
                                      </div>

                                      <div class="form-group">
                                        <a href="room_update_stuff_form.php?roomid=<?= $result['RoomID'] ?>">แก้ไขครุภัณฑ์</a> <br>
                                      </div>

                                      <input type="hidden" name="roomid1" value="<?= $result['RoomID']?>">
                                      <button type="submit" class="btn btn-default">ตกลง</button>
                                      <a href="building.php" class="btn btn-default">ยกเลิก</a>
                                    </form>
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

    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>

    <script type="text/javascript">
    $(document).ready(function() {
        $('#building').DataTable();
    } );

    function changevalue(value) {
      $('input[name=id]').val(value);
    }
    </script>

</body>

</html>
