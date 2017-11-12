<!DOCTYPE html>
<?php
require 'DBconnect.php';
session_start();
if (!isset($_SESSION["user_id"])){
  header('Location: login.php');
  die();
}

if ($_SESSION['user_type'] != 9) {
  header('Location: login.php');
  die();
}
try {
  $stmt = $db->prepare("SELECT * FROM v_room ");
  $stmt->bindParam(':id',$id);
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
    <nav class="navbar navbar-default" role="navigation">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <!-- navbar-brand is hidden on larger screens, but visible when the menu is collapsed -->
                <a class="navbar-brand" href="index.html">Business Casual</a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                  <li>
                      <a href="building.php">จัดการอาคารที่พัก</a>
                  </li>
                  <li>
                      <div class="dropdown" style="padding: 20px;">
                        <a href="#" class="dropdown-toggle" id="financeLink" data-toggle="dropdown"
                        style="color: #777">
                          จัดการด้านการเงิน <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="financeLink">
                          <li><a href="resident_monthly_expense.php">จัดการค่าห้องประจำเดือน</a></li>
                          <li role="separator" class="divider"></li>
                          <li><a href="building_water_bill.php">จัดการค่าน้ำ</a></li>
                          <li><a href="building_electric_bill.php">จัดการค่าไฟ</a></li>
                        </ul>
                      </div>
                  </li>
                  <li>
                      <a href="form_handle.php">จัดการแบบฟอร์ม</a>
                  </li>
                  <li>
                      <a href="member.php">จัดการสมาชิก</a>
                  </li>
                  <li>
                      <a href="logout.php">ออกจากระบบ</a>
                  </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>

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
                                  <font color ="#0080ff"><h2 class="intro-text text-center">การเพิ่มห้องพัก</h2></font>
                                  <hr>
                                   </div>
                                      <div class="col-lg-6">

                                        <form role="form" action= "room_insert.php" method="post">

                                          <div class="form-group">
                                              <label>เลือกแฟลต</label>
                                              <select class="form-control" name="build1">
                                                <?php foreach ($resultBuilding as $building): ?>
                                                  <option value="<?= $building['BuildingID'] ?>"><?= $building['BuildingName'] ?></option>
                                                <?php endforeach; ?>
                                              </select>
                                          </div>

                                            <div class="form-group">
                                                <label>เพิ่มเลขห้อง</label>
                                                <input class="form-control" name="roomname1">
                                                <p class="help-block">ตัวอย่าง : 101 </p>
                                            </div>

                                            <div class="form-group">
                                                <label>กำหนดชั้น</label>
                                                <input class="form-control" name="floor1">
                                                <p class="help-block">ตัวอย่าง : 1 </p>
                                            </div>

                                            <div class="form-group">
                                                <label>เลือกประเภทห้อง</label>
                                                <select class="form-control" name= "roomtype1">
                                                  <option value="1">ห้องโสด</option>
                                                  <option value="2">ห้องครอบครัว</option>
                                                  <option value="3">เรือนรับรอง</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label>เลือกสถานะห้องพัก</label>
                                                <select class="form-control" name="roomstatus1">
                                                  <option value="1">ว่าง</option>
                                                  <option value="2">ไม่ว่าง</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label>กำหนดค่าเช่าห้องห้องพัก</label>
                                                <input class="form-control" name="roomrate1">
                                                <p class="help-block">ตัวอย่าง : 1000 </p>
                                            </div>

                                            <div class="form-group">
                                                <label>กำหนดค่าของเสียหาย</label>
                                                <input class="form-control" name="insurate1">
                                                <p class="help-block">ตัวอย่าง : 1000 </p>
                                            </div>

                                            <button type="submit" class="btn btn-default">ตกลง</button>
                                            <button type="reset" class="btn btn-default">ยกเลิก</button>
                                                  <br></br>






                                </table>
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
