<?php
require 'DBconnect.php';

session_start();
if (!isset($_SESSION["user_id"])){
  header('Location: login.php');
  die();
}

if (!in_array($_SESSION['user_type'], [2, 3, 4, 5, 9])) {
  header('Location: login.php');
  die();
}

try {
  $stmt = $db->prepare("SELECT * FROM v_room where RoomID = :id");
  $stmt->bindParam(':id', $_GET['roomid']);
  $stmt->execute();
  $stmt->setFetchMode(PDO::FETCH_ASSOC);
  $room = $stmt->fetch();

  $stmt = $db->prepare("SELECT * FROM room_stuff where RoomID = :id");
  $stmt->bindParam(':id', $_GET['roomid']);
  $stmt->execute();
  $stmt->setFetchMode(PDO::FETCH_ASSOC);
  $stuffs = $stmt->fetchAll();
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

    <title>จัดการครุภัณฑ์ห้องพัก</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/business-casual.css?v=1234" rel="stylesheet">

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
    <?php if ($_SESSION['user_type'] == 2 || $_SESSION['user_type'] == 3): ?>
      <?php require 'user_2_nav.php'; ?>
    <?php else: ?>
      <?php require 'user_'. $_SESSION['user_type'] .'_nav.php'; ?>
    <?php endif ?>

    <div class="container">
      <div class="box">
        <div class="col-lg-12">
          <hr>
          <h2 class="intro-text text-center" style="color: #0080ff">
            แก้ไขครุภัณฑ์ห้อง <?= $room['RoomName'] ?> <?= $room['BuildingName'] ?>
            <br> 
            <small><a href="room_update_form.php?id=<?= $room['RoomID'] ?>">กลับ</a></small>
          </h2>
          <hr>
        </div>
        <div class="row">
          <div class="col-lg-12">
            <button class="btn btn-default pull-right" data-toggle="modal" data-target="#addmodal">
              เพิ่มครุภัณฑ์
            </button>
          </div>
        </div>
        <div class="row" style="margin-top: 12px;">
          <div class="col-lg-12">
            <table class="table table-bordered" style="background: #fff">
              <thead>
                <th>ชื่อครุภัณฑ์</th>
                <th>แก้ไข</th>
              </thead>
              <tbody>
              <?php foreach ($stuffs as $stuff): ?>
              <tr>
                <td><?= $stuff['Name'] ?></td>
                <td>
                  <button class="btn btn-primary" onclick="showEditModal(event, <?= $stuff['id'] ?>)">แก้ไข</button>
                  <form action="room_delete_stuff.php" method="POST" style="display: inline" onsubmit="return confirm('ต้องการลบคุรุภัณฑ์นี้หรือไม่')">
                    <input type="hidden" name="id" value="<?= $stuff['id'] ?>">
                    <button class="btn btn-danger" type="submit">ลบ</button>
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

    <div id="modal" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">แก้ไขครุภัณฑ์</h4>
          </div>
          <div class="modal-body" id="modalContent">
          </div>
        </div>

      </div>
    </div>
    
    <div id="addmodal" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">เพิ่มครุภัณฑ์</h4>
          </div>
          <div class="modal-body">
            <form action="room_add_stuff.php" method="POST">
              <input type="hidden" name="id" value="<?= $room['RoomID'] ?>">
              <div class="form-group">
                <label>ชื่อครุภัณฑ์</label>
                <input type="text" name="name" class="form-control">
              </div>

              <div class="form-group">
                <button type="submit" class="btn btn-primary">
                  เพิ่ม
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div id="editmodal" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">แก้ไขครุภัณฑ์</h4>
          </div>
          <div class="modal-body">
            
          </div>
        </div>
      </div>
    </div>


    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>

    <script type="text/javascript">
    function showEditModal(event, id) {
      event.preventDefault();
      
      var url = "get_room_stuff_edit_form.php?id=" + id;

      $.get(url, function(res) {
        $('#editmodal .modal-body').html('');
        $('#editmodal .modal-body').html(res);

        $('#editmodal').modal('show');
      });
    }
    </script>

</body>

</html>
