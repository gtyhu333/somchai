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
  $sql = "
  SELECT `return_form`.`ReturnID`, `return_form`.`CreateDate`,`v_resident`.`Name`, `v_resident`.`Building`,`v_resident`.`BuildingID`,
  `v_resident`.`RoomID`,`return_form`.`ReturnDate`,`return_form`.`Cause`,`return_form`.`Status`, 
  `return_form_checker`.`submitted` as `checker_submitted`, `return_form_manager`.`submitted` as `manager_submitted`
  FROM return_form
  INNER JOIN `v_resident` ON `return_form`.`ResidentID` = `v_resident`.`ResidentID` 
  INNER JOIN `return_form_checker` ON `return_form`.`ReturnID` = `return_form_checker`.`return_form_id` 
  INNER JOIN `return_form_manager` ON `return_form`.`ReturnID` = `return_form_manager`.`return_form_id`
  ORDER BY `return_form`.`CreateDate` DESC
";

  if (isset($_GET['building']) && $_GET['building'] != 'all') {
    $sql .= " WHERE BuildingID = {$_GET['building']}";
  }
  $stmt = $db->prepare($sql);
  $stmt->execute();
  $repairforms = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
              <h2 class="intro-text text-center" style="color: #0080ff">การจัดการแบบฟอร์มขอคืนห้อง</h2>
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

            <div class="row" style="margin-top: 16px;">
              <div class="col-md-4">
                <label>ชื่ออาคาร</label>
                <form action="form_return_handle_admin.php" method="get" id="buildingform">
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
            </div>

            <div class="row" style="margin-top: 16px;">
              <div class="col-md-12">
                <table class="table table-bordered table-stripped table-hover" id="repairtable">
                  <thead>
                    <th>แฟลต</th>
                    <th>ห้อง</th>
                    <th>ผู้แจ้ง</th>
                    <th>วันที่ขอคืนห้อง</th>
                    <th>สาเหตุ</th>
                    <th>วันที่แจ้ง</th>
                    <th>สถานะ</th>
                    <th>Options</th>
                  </thead>
                  <tbody>
                    <?php foreach ($repairforms as $form): ?>
                    <tr>
                      <td><?= $form['Building'] ?></td>
                      <td><?= roomNumber($form['RoomID'], $db) ?></td>
                      <td><?= $form['Name'] ?></td>
                      <td><?= sqlDateToThaiDate($form['ReturnDate']) ?></td>
                      <td><?= $form['Cause'] ?></td>
                      <td><?= sqlDateToThaiDate($form['CreateDate']) ?></td>
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
                        <br>
                        <?php if (!!$form['checker_submitted']): ?>
                          <span class="text-success">อนุกรรมการอนุมัติแล้ว<!-- <a href="#" onclick="showCheckerForm(event, <?= $form['ReturnID'] ?>)">ดูฟอร์ม</a> --></span>
                        <?php else: ?>
                          <span class="text-danger">อนุกรรมการยังไม่อนุมัติ</span>
                        <?php endif ?>
                        <br>
                        <?php if (!!$form['manager_submitted']): ?>
                          <span class="text-success">ประธานอนุกรรมการอนุมัติแล้ว <!-- <a href="#" onclick="showManagerForm(event, <?= $form['ReturnID'] ?>)">ดูฟอร์ม</a> --></span>
                        <?php else: ?>
                          <span class="text-danger">ประธานอนุกรรมการยังไม่อนุมัติ</span>
                        <?php endif ?>
                        <br>
                        <?php if ($form['Status'] != 3): ?>
                          <a href="#" onclick="showModal(event, <?= $form['ReturnID'] ?>)">เปลี่ยนสถานะ</a>
                        <?php endif ?>
                        <br><a href="#" onclick="showForm(event, <?= $form['ReturnID'] ?>)">ดูแบบฟอร์ม</a>
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
              <form action="form_return_change_status.php" method="post" class="repairmodal-content">

              </form>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="flatcheckermodal">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">ความเห็นของอนุกรรมการประจำอาคาร</h4>
          </div>
          <div class="modal-body">
            <div class="flatcheckermodal-content">

            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="flatmanagermodal">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">ความเห็นของอนุกรรมการประจำอาคาร</h4>
          </div>
          <div class="modal-body">
            <div class="flatmanagermodal-content">

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
            <h4 class="modal-title">แบบฟอร์มขอคืนห้อง</h4>
          </div>
          <div class="modal-body">
            <div class="formmodal-content">

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

      var url = "get_return_status_form.php?id=" + id;

      $.get(url, function(response) {
        $('.repairmodal-content').html('');
        $('.repairmodal-content').html(response);

        $('#repairpicmodal').modal('show');
      });
    }

    function showCheckerForm(event, id) {
      event.preventDefault();

      var url = "get_return_checker_form_view.php?id=" + id;

      $.get(url, function(response) {
        $('.flatcheckermodal-content').html('');
        $('.flatcheckermodal-content').html(response);

        $('#flatcheckermodal').modal('show');
      });
    }

    function showManagerForm(event, id) {
      event.preventDefault();

      var url = "get_return_manager_form_view.php?id=" + id;

      $.get(url, function(response) {
        $('.flatmanagermodal-content').html('');
        $('.flatmanagermodal-content').html(response);

        $('#flatmanagermodal').modal('show');
      });
    }

    function showForm(event, id) {
      event.preventDefault();

      var url = "get_return_form_view.php?id=" + id;

      $.get(url, function(response) {
        $('#formmodal .formmodal-content').html('');
        $('#formmodal .formmodal-content').html(response);

        $('#formmodal').modal('show');
      });
    }
    </script>

</body>

</html>
