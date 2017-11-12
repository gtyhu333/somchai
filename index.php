<?php

require 'DBconnect.php';

$range = [
    '1' => [date('Y') . '-01-01', date('Y') . '-04-01'],
    '2' => [date('Y') . '-04-02', date('Y') . '-08-01'],
    '3' => [date('Y') . '-08-02', date('Y') . '-12-31'],
];
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>index main</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/business-casual.css" rel="stylesheet">

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
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                  <li>
                      <a href="index.php">หน้าหลัก</a>
                  </li>
                    <li>
                       <a href="form_request.php">  <u><b>แบบฟอร์มขอเข้าพัก</b></u></a>
                    </li>
                    <li>
                        <a href="building_normal.php">สถานะหอพักบุคลากร</a>
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
                <div class="col-lg-12">

                      <font color ="#0080ff"><center><h4>ประกาศข่าวสารจากหอพักบุคลากรมหาวิทยาลัยอุบลราชธานี</h4></center></font>
                      <p>sssssssssssss</p>
                      <p>sssssssssssss</p>
                      <p>sssssssssssss</p>
                      <p>sssssssssssss</p>

                      <div class="col-md-6">
                        <form action="score_show" method="GET" style="display: inline-block;">
                          <input type="hidden" name="id" value="1">
                          <button class="btn btn-primary" type="submit">ดูผลคะแนนการขอเข้าพัก รอบล่าสุด</button>
                        </form>

<?php
try {
  $stmt = $db->prepare("
    SELECT * FROM `v_request` WHERE `request_status` = 'ปกติ'
    AND `RequestDate` BETWEEN '{$range[1][0]}' AND '{$range[1][1]}';"
    );
  $stmt->execute();
  $stmt->setFetchMode(PDO::FETCH_ASSOC);
  $requests = $stmt->fetchAll();
}
catch(PDOException $e) {
  echo "Error: " . $e->getMessage();
}
?>

                    <table class="table table-bordered table-hover table-striped" id="room" style="background-color: #fff">
                        <thead>
                            <th colspan="5">รอบที่ 1 ม.ค. - เม.ษ.</th>
                        </thead>
                        <thead>
                          <th><center>ชื่อ</center></th>
                          <th><center>สกุล</center></th>
                          <th><center>คณะ</center></th>
                          <th><center>วันที่ยื่นฟอร์ม</center></th>
                          <th><center>คะแนน</center></th>
                          <th><center>วันที่ประมวลผลคะแนน<center></th>
                        </thead>
                          <tbody>
                          <?php foreach ($requests as $request) : ?>
                            <tr>
                              <td><center><?= $request['Name'] ?></center></td>
                              <td><center><?= $request['Surname'] ?></center></td>
                              <td><center><?= $request['FacNameT'] ?></center></td>
                              <td><center><?= sqlDateToThaiDate($request['RequestDate']) ?></center></td>
                              <?php $score = getScore($request['StaffID'], $db) ?>
                              <td><center><?= $score['Score'] ?></center></td>
                            </tr>
                          <?php endforeach; ?>
                          <?php if (empty($requests)): ?>
                              <tr>
                                  <td colspan="5"><center>ไม่มีข้อมูล</center></td>
                              </tr>
                          <?php endif ?>

<?php
try {
  $stmt = $db->prepare("
    SELECT * FROM `v_request` WHERE `request_status` = 'ปกติ'
    AND `RequestDate` BETWEEN '{$range[2][0]}' AND '{$range[2][1]}';"
    );
  $stmt->execute();
  $stmt->setFetchMode(PDO::FETCH_ASSOC);
  $requests = $stmt->fetchAll();
}
catch(PDOException $e) {
  echo "Error: " . $e->getMessage();
}
?>
                        <thead>
                            <th colspan="5">รอบที่ 2 พ.ค. - ส.ค.</th>
                        </thead>
                          <?php foreach ($requests as $request) : ?>
                            <tr>
                              <td><center><?= $request['Name'] ?></center></td>
                              <td><center><?= $request['Surname'] ?></center></td>
                              <td><center><?= $request['FacNameT'] ?></center></td>
                              <td><center><?= sqlDateToThaiDate($request['RequestDate']) ?></center></td>
                              <?php $score = getScore($request['StaffID'], $db) ?>
                              <td><center><?= $score['Score'] ?></center></td>
                            </tr>
                          <?php endforeach; ?>
                          <?php if (empty($requests)): ?>
                              <tr>
                                  <td colspan="5"><center>ไม่มีข้อมูล</center></td>
                              </tr>
                          <?php endif ?>

<?php
try {
  $stmt = $db->prepare("
    SELECT * FROM `v_request` WHERE `request_status` = 'ปกติ'
    AND `RequestDate` BETWEEN '{$range[3][0]}' AND '{$range[3][1]}';"
    );
  $stmt->execute();
  $stmt->setFetchMode(PDO::FETCH_ASSOC);
  $requests = $stmt->fetchAll();
}
catch(PDOException $e) {
  echo "Error: " . $e->getMessage();
}
?>
                        <thead>
                            <th colspan="5">รอบที่ 3 ก.ย. - ธ.ค.</th>
                        </thead>
                          <?php foreach ($requests as $request) : ?>
                            <tr>
                              <td><center><?= $request['Name'] ?></center></td>
                              <td><center><?= $request['Surname'] ?></center></td>
                              <td><center><?= $request['FacNameT'] ?></center></td>
                              <td><center><?= sqlDateToThaiDate($request['RequestDate']) ?></center></td>
                              <?php $score = getScore($request['StaffID'], $db) ?>
                              <td><center><?= $score['Score'] ?></center></td>
                            </tr>
                          <?php endforeach; ?>
                          <?php if (empty($requests)): ?>
                              <tr>
                                  <td colspan="5"><center>ไม่มีข้อมูล</center></td>
                              </tr>
                          <?php endif ?>
                        </tbody>
                    </table>
                </div>
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

    <!-- Script to Activate the Carousel -->
    <script>
    $('.carousel').carousel({
        interval: 5000 //changes the speed
    })
    </script>

</body>

</html>
