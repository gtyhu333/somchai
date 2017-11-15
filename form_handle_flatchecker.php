<!DOCTYPE html>
<?php
require 'DBconnect.php';

session_start();
if (!isset($_SESSION["user_id"])){
  header('Location: login.php');
  die();
}

if ($_SESSION['user_type'] != 4) {
  header('Location: login.php');
  die();
}
try {
  $sql = "SELECT * FROM member WHERE UserID = :userid";
  $stmt = $db->prepare($sql);
  $stmt->bindParam(':userid', $_SESSION['user_id']);
  $stmt->execute();
  $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

  $sql = "
  SELECT `return_form`.`ReturnID`,`v_resident`.`Name`, `v_resident`.`Building`,`v_resident`.`BuildingID`,
  `v_resident`.`RoomID`,`return_form`.`ReturnDate`,`return_form`.`Cause`,`return_form`.`Status`, 
  `return_form_checker`.`submitted` as `checker_submitted`, `return_form_manager`.`submitted` as `manager_submitted`
  FROM return_form
  INNER JOIN `v_resident` ON `return_form`.`ResidentID` = `v_resident`.`ResidentID` 
  INNER JOIN `return_form_checker` ON `return_form`.`ReturnID` = `return_form_checker`.`return_form_id` 
  INNER JOIN `return_form_manager` ON `return_form`.`ReturnID` = `return_form_manager`.`return_form_id`
";
  $sql .= " WHERE BuildingID = {$userInfo['BuildingID']}";

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
    <script src="js/jquery.js"></script>

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
              <a href="form_handle_flatchecker.php">จัดการแบบฟอร์ม</a>
            </li>
            <li>
              <a href="form_switch.php">ความเคลื่อนไหวในหอพัก</a>
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
      <div class="box">
        <div class="panel panel-default">
          <div class="panel-body">
            <hr>
              <h2 class="intro-text text-center" style="color: #0080ff">การจัดการแบบฟอร์มขอคืนห้อง</h2>
            <hr>

            <div class="row">
              <div class="col-md-12">
                <label style="display: block">เลือกประเภทฟอร์ม</label>
                <a href="form_handle_flatchecker.php" class="btn btn-primary">ฟอร์มขอคืนห้อง</a>
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
                    <th>สถานะ</th>
                  </thead>
                  <tbody>
                    <?php foreach ($repairforms as $form): ?>
                    <tr>
                      <td><?= $form['Building'] ?></td>
                      <td><?= roomNumber($form['RoomID'], $db) ?></td>
                      <td><?= $form['Name'] ?></td>
                      <td><?= sqlDateToThaiDate($form['ReturnDate']) ?></td>
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
                        <br>
                        <?php if (!!$form['checker_submitted']): ?>
                          <span class="text-success">อนุกรรมการอนุมัติแล้ว<a href="#" onclick="showCheckerForm(event, <?= $form['ReturnID'] ?>)">ดูฟอร์ม</a></span>
                        <?php else: ?>
                          <span class="text-danger">อนุกรรมการยังไม่อนุมัติ</span>
                        <?php endif ?>
                        <br>
                        <?php if (!!$form['manager_submitted']): ?>
                          <span class="text-success">ประธานอนุกรรมการอนุมัติแล้ว <a href="#" onclick="showManagerForm(event, <?= $form['ReturnID'] ?>)">ดูฟอร์ม</a></span>
                        <?php else: ?>
                          <span class="text-danger">ประธานอนุกรรมการยังไม่อนุมัติ</span>
                        <?php endif ?>
                        <br>
                        <?php if (! !!$form['checker_submitted']): ?>
                          <a href="#" onclick="showModal(event, <?= $form['ReturnID'] ?>)">กรอกแบบฟอร์ม</a>
                        <?php endif ?>
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
            <h4 class="modal-title">กรอกฟอร์มความเห็น</h4>
          </div>
          <div class="modal-body">
            <div class="repairmodal-content">
      
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

      var url = "get_return_checker_form.php?id=" + id;

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
        $('.flatcheckermodal-content').html('');
        $('.flatcheckermodal-content').html(response);

        $('#flatcheckermodal').modal('show');
      });
    }
    </script>

</body>

</html>
