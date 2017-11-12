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
  $sql = "SELECT * FROM switch_form";

  // if (isset($_GET['building']) && $_GET['building'] != 'all') {
  //   $sql .= " WHERE BuildingID = {$_GET['building']}";
  // }
  $stmt = $db->prepare($sql);
  $stmt->execute();
  $forms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
  echo "Error: {$e->getMessage()}";
  die();
}

try {
  $stmt = $db->prepare("SELECT * FROM building");
  $stmt->execute();
  $stmt->setFetchMode(PDO::FETCH_ASSOC);
  $buildings = $stmt->fetchAll();
} catch(PDOException $e) {
  echo "Error: " . $e->getMessage();
}

?>
}
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
      <div class="box">
        <div class="panel panel-default">
          <div class="panel-body">
            <hr>
              <h2 class="intro-text text-center" style="color: #0080ff">การจัดการแบบฟอร์มขอสลับห้อง</h2>
            <hr>

            <div class="row">
              <div class="col-md-12">
                <label style="display: block">เลือกประเภทฟอร์ม</label>
                <a href="form_handle.php" class="btn btn-primary">ฟอร์มร้องขอ</a>
                <a href="form_repair_handle.php" class="btn btn-primary">ฟอร์มแจ้งซ่อม</a>
                <a href="form_return_handle_admin.php" class="btn btn-primary">ฟอร์มขอคืนห้อง</a>
                <a href="form_switch_handle_admin.php" class="btn btn-primary">ฟอร์มขอสลับห้อง</a>
              </div>
            </div>

            <!-- <div class="row" style="margin-top: 16px;">
              <div class="col-md-4">
                <label>ชื่ออาคาร</label>
                <form action="form_switch_handle_admin.php" method="get" id="buildingform">
                  <select class="form-control" name="building" onchange="document.getElementById('buildingform').submit()">
                    <option value="all"<?= ! isset($_GET['building']) ? ' selected' : '' ?>>แฟลตทั้งหมด</option>
                    <?php foreach ($buildings as $building): ?>
                      <option value="<?= $building['BuildingID'] ?>"<?= isset($_GET['building']) &&  $_GET['building'] == $building['BuildingID'] ? ' selected' : '' ?>>
                        <?= $building['BuildingName'] ?>
                      </option>
                    <?php endforeach ?>
                  </select>
                </form>
              </div>
            </div> -->

            <div class="row" style="margin-top: 16px;">
              <div class="col-md-12">
                <table class="table table-bordered table-stripped table-hover" id="repairtable">
                  <thead>
                    <th>แฟลต</th>
                    <th>ห้อง</th>
                    <th>ไปยังแฟลต</th>
                    <th>ไปยังห้อง</th>
                    <th>ผู้แจ้ง</th>
                    <th>วันที่ขอสลับห้อง</th>
                    <th>สาเหตุ</th>
                    <th>สถานะ</th>
                    <th>จัดการข้อมูล</th>
                  </thead>
                  <tbody>
                    <?php foreach ($forms as $form): ?>
                    <tr>
                      <td><?= getRoomBuildingName($form['RoomID'], $db) ?></td>
                      <td><?= roomNumber($form['RoomID'], $db) ?></td>
                      <td><?= getRoomBuildingName($form['toRoomID'], $db) ?></td>
                      <td><?= roomNumber($form['toRoomID'], $db) ?></td>
                      <?php $info = getResidentInfo($form['ResidentID'], $db) ?>
                      <td><?= $info['Name'] ?></td>
                      <td><?= sqlDateToThaiDate($form['SwitchingDate']) ?></td>
                      <td><?= $form['Cause'] ?></td>
                      <td>
                        <?php if ($form['Status'] == 1): ?>
                          <span class="text-danger">ยังไม่อนุมัติ</span>
                        <?php endif ?>
                        <?php if ($form['Status'] == 2): ?>
                          <span class="text-success">อนุมัติ</span>
                        <?php endif ?>
                        <?php if ($form['Status'] == 3): ?>
                          <span class="text-info">ยกเลิก</span>
                        <?php endif ?>

                        <?php if ($form['Status'] != 3): ?>
                          <a href="#" onclick="showModal(event, <?= $form['SwitchID'] ?>)">เปลี่ยนสถานะ</a>
                        <?php endif ?>
                      </td>
                      <td>
                        <form action="return_form_delete.php" method="POST">
                          <input type="hidden" name="id" value="<?= $form['ReturnID'] ?>">
                          <button type="submit" name="delete" class="btn btn-danger">
                            ลบ
                          </button>
                        </form>
                      </td>
                    </tr>
                    <?php endforeach ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /.container -->

    <div class="modal fade" tabindex="-1" role="dialog" id="repairpicmodal">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">แก้ไขสถานะ</h4>
          </div>
          <div class="modal-body">
            <div>
              <form action="form_switch_change_status.php" method="post" class="repairmodal-content">

              </form>
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

    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>


    <script type="text/javascript">
    $(document).on('ready', function() {
      $('#repairtable').DataTable();
    });

    function showModal(event, id) {
      event.preventDefault();

      var url = "get_switch_status_form.php?id=" + id;

      $.get(url, function(response) {
        $('.repairmodal-content').html('');
        $('.repairmodal-content').html(response);

        $('#repairpicmodal').modal('show');
      });
    }
    </script>

</body>

</html>
