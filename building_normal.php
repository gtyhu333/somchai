<!DOCTYPE html>
<?php
require 'DBconnect.php';

try {
  $stmt = $db->prepare("
SELECT DISTINCT BuildingName, 
SUM(case RoomType when 1 then 1 else 0 end) as singleroom,
SUM(case RoomType when 2 then 1 else 0 end) as familyroom,
SUM(case RoomStatus when 'ว่าง พร้อมใช้งาน' then 1 else 0 end) as vacant
FROM (SELECT building.BuildingName, room.RoomID, room.Floor, room.RoomName, allvars.ValueT as RoomStatus, room.RoomType,
room.RoomRate, room.InsurantRate

FROM building, room, allvars

WHERE room.BuildingID = building.BuildingID
AND allvars.FieldName = 'RoomStatus'
AND room.RoomStatus = allvars.FieldCode) as t1
GROUP BY BuildingName
  ");
  $stmt->execute();
  $stmt->setFetchMode(PDO::FETCH_ASSOC);
  $result = $stmt->fetchAll();
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
                      <a href="index.php">หน้าหลัก</a>
                  </li>
                    <li>
                        <a href="building_normal.php">สถานะหอพักบุคลากร</a>
                    </li>
                    <li>
                       <a href="form_request.php">  <u><b>ยื่นแบบฟอร์มขอเข้าพัก</b></u></a>
                    </li>
                    <li>
                        <a href="login.php">เข้าสู่ระบบ</a>
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
                              <hr>
                              <font color ="#0080ff"><h2 class="intro-text text-center">สถานะหอพักบุคลากร</h2></font>
                              <hr>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-striped" id="building">
                                  </div>
                                      </div>


                                        <div class="panel-body">
                                            <div class="col-md-6">
                                            <table class="table">
                                              <thead><th colspan="4">สรุปสถานะอาคาร</th></thead>
                                              <thead>
                                                <th>แฟลต</th>
                                                <th>จำนวนห้องโสด</th>
                                                <th>จำนวนห้องครอบครัว</th>
                                                <th><font color ="green">ห้องพักว่าง</font></th>
                                              </thead>
                                              <tbody>
                                                <?php foreach ($result as $building): ?>
                                                <tr>
                                                  <td><?= $building['BuildingName'] ?></td>
                                                  <td><?= $building['singleroom'] ?> ห้อง</td>
                                                  <td><?= $building['familyroom'] ?> ห้อง</td>
                                                  <td style="color: green"><?= $building['vacant'] ?> ห้อง</td>
                                                </tr>
                                                <?php endforeach ?>
                                              </tbody>
                                            </table>
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

    <form action="building_normal.php" method="get" id="redirectform">
      <input type="hidden" name="building" value="">
    </form>

    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>

    <script type="text/javascript">
    $(document).ready(function() {
        $('#room').DataTable();
        $('input[name=id]').val($('#selectid').val());
    } );

    function changevalue(value) {
      $('input[name=building]').val($('#selectid').val());
      $('#redirectform').submit();
    }
    </script>

</body>

</html>
