<!DOCTYPE html>
<?php
require 'DBconnect.php';

try {
  $stmt = $db->prepare("SELECT * FROM staff WHERE `request_status` = 'ปกติ';");
  $stmt->execute();
  $stmt->setFetchMode(PDO::FETCH_ASSOC);
  $requests = $stmt->fetchAll();
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

  <div class="brand">หอพักบุคลากร</div>
  <div class="address-bar">มหาวิทยาลัยอุบลราชธานี</div>

    <!-- Navigation -->
    <?php require 'admin_nav.php'; ?>

    <div class="container">

        <div class="row">
            <div class="box">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-striped" id="building">
                                        <thead>

                                          <div class="col-lg-12">
                                        <hr>
                                        <font color ="#0080ff"><h2 class="intro-text text-center">การประมวลผลคะแนน</h2></font>
                                        <hr>
                                         </div>
                                            <div class="form-group">

                                              <div class="row">
                                                <div class="col-md-6">
                                                </div>

                                        </div>
                                              </div>
                                            </div>
                                          </div>
                                        </thead>
                                        <div class="panel-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover table-striped" id="room">
                                                    <thead>
                                                      <th><center>ชื่อ - สกุล</center></th>
                                                      <th><center>ตำแหน่ง</center></th>
                                                      <th><center>คณะ</center></th>
                                                      <th><center>วันที่บรรจุ</center></th>
                                                      <th><center>อยู่ตามทะเบียนบ้าน</center></th>
                                                      <th><center>วันที่ยื่นฟอร์ม</center></th>
                                                      <th><center>คะแนน</center></th>
                                                      <th><center>วันที่คำนวณ</center></th>
                                                      <th><center>การจัดสรร</center></th>
                                                      <th><center>ยกเลิกฟอร์ม</center></th>
                                                    </thead>
                                                      <tbody>
                                                      <?php foreach ($requests as $request) : ?>
                                                        <tr>
                                                          <td><center><?= $request['Name'] . ' ' . $request['Surname']  ?></center></td>
                                                          <td><center><?= positionName($request['PositionID'], $db) ?></center></td>
                                                          <td><center><?= facName($request['FacID'], $db) ?></center></td>
                                                          <td><center><?= sqlDateToThaiDate($request['EmployDate']) ?></center></td>
                                                          <td><center><?= getFullAddress($request, $db) ?></center></td>
                                                          <td><center><?= sqlDateToThaiDate($request['RequestDate']) ?></center></td>
                                                          <?php $score = getScore($request['StaffID'], $db) ?>
                                                          <td><center><?= $score['Score'] ?></center></td>
                                                          <td><center><?= sqlDateToThaiDate($score['EvaluateDate']) ?></center></td>
                                                          <td><center><?= $request['request_status'] ?></center></td>
                                                          <td><center>
                                                            <form action="form_cancle.php" method="POST">
                                                              <input type="hidden" value="<?= $request['StaffID'] ?>" name="id">
                                                              <button class="btn btn-danger" type="submit">
                                                                ยกเลิก
                                                              </button>
                                                            </form>
                                                          </center></td>
                                                        </tr>
                                                      <?php endforeach; ?>
                                                    </tbody>
                                                </table>

                                                <br></br>


                                                  <div class="col-md-12" style="display: flex;">
                                                    <form action="score_process.php" method="POST" style="display: inline-block; margin: 0 auto;">
                                                    <label>เลือกรอบสำหรับการประมวลผล</label>
                                                    <select class="form-control" name="range" onchange="changevalue(this.value);">
                                                      <option value="1">ม.ค. - เม.ษ.</option>
                                                      <option value="2">พ.ค. - ส.ค.</option>
                                                      <option value="3">ก.ย. - ธ.ค.</option>
                                                    </select>
                                                    <br>
                                                    <button type="submit" class="btn btn-primary">  ทำการประมวลผลคะแนน</button>  &nbsp;&nbsp;
                                                      <br></br>
                                                    </form>

                                            </div>
                                        </div>
                                    </table>
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

    <form action="building.php" method="get" id="redirectform">
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
        $('input[name=cycle]').val($('#selectid').val());
    } );

    function changevalue(value) {
      $('input[name=building]').val($('#selectid').val());
    }
    </script>

</body>

</html>
