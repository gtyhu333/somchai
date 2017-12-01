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
                                                      <th><center>ที่อยู่</center></th>
                                                      <th><center>วันที่ยื่นฟอร์ม</center></th>
                                                      <th><center>คะแนน (ล่าสุด)</center></th>
                                                      <th><center>วันที่คำนวณ (ล่าสุด)</center></th>
                                                      <th><center>การจัดสรร</center></th>
                                                      <th><center>จัดการฟอร์ม</center></th>
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
                                                          <td>
                                                            <center>
                                                              <?php if ($score): ?>
                                                                <?= $score['Score'] ?> 
                                                                <br><a href="#" onclick="showScoreModal(event, <?= $request['StaffID'] ?>)">ดูรายละเอียด</a>
                                                              <?php endif ?>
                                                            </center></td>
                                                          <td>
                                                            <?php if ($score): ?>
                                                              <center><?= sqlDateToThaiDate($score['EvaluateDate']) ?> <br> 
                                                            <?= date('H:i', strtotime($score['EvaluateDate'])) ?></center>
                                                            <?php endif ?>
                                                          </td>
                                                          <td><center><?= $request['request_status'] ?></center></td>
                                                          <td><center>
                                                            <?php if ($request['request_status'] != 'ยกเลิก' && $request['request_status'] != 'จัดสรร'): ?>
                                                                <button class="btn btn-success" data-toggle="modal" data-target="#modalRoom<?= $request['StaffID'] ?>">
                                                                  จัดสรร
                                                                </button>
                                                              <?php endif ?>
                                                            <form action="form_cancle.php" method="POST">
                                                              <input type="hidden" value="<?= $request['StaffID'] ?>" name="id">
                                                              <button class="btn btn-danger" type="submit">
                                                                ยกเลิก
                                                              </button>
                                                            </form>
                                                          </center></td>
                                                        </tr>
                                                        <?php if ($request['request_status'] != 'ยกเลิก'): ?>
                                                          <div id="modalRoom<?= $request['StaffID'] ?>" class="modal fade" role="dialog">
                                                          <div class="modal-dialog">

                                                            <!-- Modal content-->
                                                            <div class="modal-content">
                                                              <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                <h4 class="modal-title">จัดสรรห้องพัก</h4>
                                                              </div>
                                                              <div class="modal-body">
                                                                <form action="form_assign_room.php" method="post">
                                                                  <div class="form-group" style="margin-bottom: 2rem">
                                                                    <label for="newstatus">เลือกห้องที่ต้องการจัดสรรให้</label>
                                                                    <select name="roomID" class="form-control">
                                                                      <?php foreach (getAvailableRoomForRequest($request, $db) as $building => $rooms): ?>
                                                                        <optgroup label="<?= getBuidlingName($building, $db) ?>">
                                                                          <?php foreach ($rooms as $room): ?>
                                                                            <option value="<?= $room['RoomID'] ?>">ห้อง <?= $room['RoomName'] ?> (<?= getRoomTypeName($room['RoomType'], $db) ?>)</option>
                                                                          <?php endforeach ?>
                                                                        </optgroup>
                                                                      <?php endforeach ?>
                                                                    </select>
                                                                    <input type="hidden" name="StaffID" value="<?= $request['StaffID']?>">
                                                                  </div>
                                                                  <button type="submit" class="btn btn-success">จัดสรร</button>
                                                                  <button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
                                                                </form>
                                                              </div>
                                                            </div>

                                                          </div>
                                                        </div>
                                                        <?php endif ?>
                                                      <?php endforeach; ?>
                                                    </tbody>
                                                </table>

                                                <br></br>


                                                  <div class="col-md-12" style="display: flex;">
                                                    <form action="score_process.php" method="POST" style="display: inline-block; margin: 0 auto;">
                                                    <label>เลือกรอบสำหรับการประมวลผล</label>
                                                    <select class="form-control" name="range">
                                                      <option value="1">ม.ค. - เม.ษ.</option>
                                                      <option value="2">พ.ค. - ส.ค.</option>
                                                      <option value="3">ก.ย. - ธ.ค.</option>
                                                    </select> <br>
                                                    <label>เลือกปีที่จะใช้ในการประมวลผล</label>
                                                    <select class="form-control" name="year">
                                                    <?php foreach (range(date('Y') - 3, date('Y') + 3) as $year): ?>
                                                    <option value="<?= $year ?>"<?= $year == date('Y') ? ' selected' : '' ?>><?= $year + 543 ?></option>
                                                    <?php endforeach ?>
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

    <div class="modal fade" tabindex="-1" role="dialog" id="scoremodal">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">รายละเอียดคะแนน</h4>
          </div>
          <div class="modal-body">
            <div id="scoremodal-content">
              
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div>

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

    function showScoreModal(event, id) {
      event.preventDefault();
      
      var url = "get_score_view.php?id=" + id;

      $.get(url, function(res) {
        $('#scoremodal-content').html('');
        $('#scoremodal-content').html(res);

        $('#scoremodal').modal('show');
      });
    }
    </script>

</body>

</html>
