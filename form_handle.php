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
  $stmt = $db->prepare("SELECT * FROM staff ORDER BY RequestDate DESC;");
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

    <style>
      .form-control-static {
        font-size: 1em;
      }
    </style>

</head>

<body>
  <div class="brand">
    <div style="display: flex; justify-content: center; margin-top: 2px;">
      <img src="logo.png" height="75">
    </div>
    <h3>หอพักบุคลากรมหาวิทยาลัยอุบลราชธานี</h3></div>

    <!-- Navigation -->
    <?php require 'admin_nav.php'; ?>

    <div class="container">

        <div class="row">
            <div class="box" style="background-color: #fff">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="">
                            <div class="">
                                <div class="table-responsive">

                                          <div class="col-lg-12">
                                        <hr>
                                        <font color ="#0080ff"><h2 class="intro-text text-center">จัดการแบบฟอร์มร้องขอ</h2></font>
                                        <hr>
                                         </div>
                                            <div class="form-group">
                                              <label>เลือกประเภทฟอร์ม</label>
                                              <div class="row">
                                                <div class="col-md-6">
                                                  <a href="form_handle.php" class="btn btn-primary">ฟอร์มร้องขอ</a>
                                                  <a href="form_repair_handle.php" class="btn btn-primary">ฟอร์มแจ้งซ่อม</a>
                                                  <a href="form_return_handle_admin.php" class="btn btn-primary">ฟอร์มขอคืนห้อง</a>
                                                  <a href="form_switch_handle_admin.php" class="btn btn-primary">ฟอร์มขอสลับห้อง</a>
                                                </div>

                                        </div>
                                              </div>
                                            </div>
                                          </div>
                                        <div class="panel-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover table-striped" id="room">
                                                    <thead>
                                                      <th><center>ชื่อ - สกุล</center></th>
                                                      <th><center>คณะ</center></th>
                                                      <th><center>วันที่ยื่นฟอร์ม</center></th>
                                                      <th><center>สถานะฟอร์ม</center></th>
                                                      <th><center>ประเภทห้องที่ร้องขอ</center></th>
                                                      <th><center>คะแนน</center></th>
                                                      <th><center>การแก้ไขสถานะฟอร์ม</center></th>
                                                      <th><center>การลบฟอร์ม</center></th>
                                                    </thead>
                                                      <tbody>
                                                      <?php foreach ($requests as $request) : ?>
                                                        <tr>
                                                          <td><center><?= $request['Name'] . ' ' . $request['Surname']?></center></td>
                                                          <td><center><?= facName($request['FacID'], $db) ?></center></td>
                                                          <td><center><?= sqlDateToThaiDate($request['RequestDate']) ?></center></td>
                                                          <td><center><?= $request['request_status'] ?></center></td>
                                                          <td><center><?= getRoomTypeName($request['RoomtypeID'], $db) ?></center></td>
                                                          <?php $score = getScore($request['StaffID'], $db) ?>
                                                          <td><center><?= $score['Score'] ? $score['Score'] : '-' ?>
                                                            <?php if ($score['Score']): ?>
                                                              <br><a href="#" onclick="showScoreModal(event, <?= $request['StaffID'] ?>)">
                                                              ดูรายละเอียด</a>
                                                            <?php endif ?>
                                                            
                                                            </center>
                                                          </td>
                                                          <td><center>
                                                            <button class="btn btn-default" onclick="showFormModal(event, <?= $request['StaffID'] ?>)">
                                                              ดูฟอร์ม
                                                            </button>
                                                              <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#modal<?= $request['StaffID'] ?>">
                                                                แก้ไข
                                                              </a>
                                                          </center></td>
                                                        <td>  <center>
                                                            <form action="form_handle_delete.php" method="post">
                                                              <input type="hidden" name="StaffID" value="<?= $request['StaffID']?>">
                                                              <button type="submit" class="btn btn-danger" name="button">ลบ</button>
                                                            </form>
                                                          </certer></td>
                                                        </tr>

                                                        <!-- Modal -->

                                                        <!-- Modal -->
                                                        <div id="modal<?= $request['StaffID'] ?>" class="modal fade" role="dialog">
                                                          <div class="modal-dialog">

                                                            <!-- Modal content-->
                                                            <div class="modal-content">
                                                              <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                <h4 class="modal-title">แก้ไขสถานะฟอร์ม</h4>
                                                              </div>
                                                              <div class="modal-body">
                                                                <form action="form_change_status.php" method="post">
                                                                  <div class="form-group" style="margin-bottom: 2rem">
                                                                    <label for="newstatus">สถานะ</label>
                                                                    <select name="newstatus" class="form-control">
                                                                      <option value="ปกติ"<?= $request['request_status'] == 'ปกติ' ? ' selected' : ''?>>ปกติ</option>
                                                                      <option value="ยกเลิก"<?= $request['request_status'] == 'ยกเลิก' ? ' selected' : ''?>>ยกเลิก</option>
                                                                    </select>
                                                                    <input type="hidden" name="StaffID" value="<?= $request['StaffID']?>">
                                                                  </div>
                                                                  <button type="submit" class="btn btn-primary">แก้ไข</button>
                                                                  <button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
                                                                </form>
                                                              </div>
                                                            </div>

                                                          </div>
                                                        </div>

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
                                                                            <option value="<?= $room['RoomID'] ?>">ห้อง <?= $room['RoomName'] ?></option>
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


                                                  <div class="col-md-12" style="display: flex;">
                                                    <form action="score_process.php" method="GET" style="display: inline-block; margin: 0 auto;">
                                                      <input type="hidden" name="id" value="1">
                                                    <a href="form_process.php" class="btn btn-primary">  การประมวลผลคะแนน</a>
                                                      <br></br>
                                                    </form>

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

    <div class="modal fade" tabindex="-1" role="dialog" id="formmodal">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">รายละเอียดฟอร์ม</h4>
          </div>
          <div class="modal-body">
            <div id="formmodal-content">
              
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

    <script type="text/javascript">
    $(document).ready(function() {
        $('input[name=id]').val($('#selectid').val());
    } );

    function changevalue(value) {
      $('input[name=building]').val($('#selectid').val());
      $('#redirectform').submit();
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

    function showFormModal(event, id) {
      event.preventDefault();

      var url = "get_request_form_view.php?id=" + id;

      $.get(url, function(res) {
        $('#formmodal-content').html('');
        $('#formmodal-content').html(res);

        $('#formmodal').modal('show');
      });
    }
    </script>

</body>

</html>
