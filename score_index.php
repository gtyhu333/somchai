<?php

require 'DBconnect.php';

$range = [
  '1' => [date('Y') . '-01-01', date('Y') . '-04-01'],
  '2' => [date('Y') . '-04-02', date('Y') . '-08-01'],
  '3' => [date('Y') . '-08-02', date('Y') . '-12-31'],
];

$mainSql = "
SELECT DISTINCT t1.StaffID, staff.PName, staff.Name, staff.Surname, staff.RequestDate, t1.Score, t1.EvaluateDate 
FROM staff 
LEFT JOIN (
    SELECT score.StaffID, score.Score, score.EvaluateDate FROM score ORDER BY score.EvaluateDate DESC
) as t1
ON t1.StaffID = staff.StaffID 
WHERE staff.request_status = 'ปกติ'
";

try {
  $stmt = $db->prepare("$mainSql AND `RequestDate` BETWEEN '{$range[1][0]}' AND '{$range[1][1]}' GROUP BY t1.StaffID;");
  $stmt->execute();
  $stmt->setFetchMode(PDO::FETCH_ASSOC);
  $requests1 = $stmt->fetchAll();

  $stmt = $db->prepare("$mainSql AND `RequestDate` BETWEEN '{$range[2][0]}' AND '{$range[2][1]}' GROUP BY t1.StaffID;");
  $stmt->execute();
  $stmt->setFetchMode(PDO::FETCH_ASSOC);
  $requests2 = $stmt->fetchAll();

  $stmt = $db->prepare("$mainSql AND `RequestDate` BETWEEN '{$range[3][0]}' AND '{$range[3][1]}' GROUP BY t1.StaffID;");
  $stmt->execute();
  $stmt->setFetchMode(PDO::FETCH_ASSOC);
  $requests3 = $stmt->fetchAll();
}
catch(PDOException $e) {
  echo "Error: " . $e->getMessage();
}
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
                 <a href="form_request.php">  <u><b>ยื่นแบบฟอร์มขอเข้าพัก</b></u></a>
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
        <div class="box" style="background-color: #fff">
          <div class="row">
            <div class="col-lg-12">
              <h4 class="text-center" style="color: hsl(211, 96%, 54%)">
                ผลการประเมินคะแนนล่าสุด
              </h4>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>ชื่อ - สกุล</th>
                    <th>วันที่ยื่นแบบฟอร์ม</th>
                    <th>คะแนนล่าสุด</th>
                    <th>วันที่ประเมินล่าสุด</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($requests1)): ?>
                  <?php foreach ($requests1 as $request): ?>
                  <tr>
                    <td><?= "{$request['PName']} {$request['Name']} {$request['Surname']}" ?></td>
                    <td><?= sqlDateToThaiDate($request['RequestDate']) ?></td>
                    <td><?= $request['Score'] ?></td>
                    <td><?= sqlDateToThaiDate($request['EvaluateDate']) ?></td>
                  </tr>
                  <?php endforeach ?>
                  <?php else: ?>
                  <?php endif ?>
                </tbody>

                <tbody>
                  <?php if (!empty($requests2)): ?>
                  <?php foreach ($requests1 as $request): ?>
                  <tr>
                    <td><?= "{$request['PName']} {$request['Name']} {$request['Surname']}" ?></td>
                    <td><?= sqlDateToThaiDate($request['RequestDate']) ?></td>
                    <td><?= $request['Score'] ?></td>
                    <td><?= sqlDateToThaiDate($request['EvaluateDate']) ?></td>
                  </tr>
                  <?php endforeach ?>
                  <?php else: ?>
                  <?php endif ?>
                </tbody>

                <tbody>
                  <?php if (!empty($requests3)): ?>
                  <?php foreach ($requests1 as $request): ?>
                  <tr>
                    <td><?= "{$request['PName']} {$request['Name']} {$request['Surname']}" ?></td>
                    <td><?= sqlDateToThaiDate($request['RequestDate']) ?></td>
                    <td><?= $request['Score'] ?></td>
                    <td><?= sqlDateToThaiDate($request['EvaluateDate']) ?></td>
                  </tr>
                  <?php endforeach ?>
                  <?php else: ?>
                  <?php endif ?>
                </tbody>
              </table>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-12 text-center">
              <a href="index.php" class="btn btn-default">กลับหน้าหลัก</a>
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
    </body>

    </html>
