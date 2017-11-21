<!DOCTYPE html>
<?php
require 'DBconnect.php';

session_start();
if (!isset($_SESSION["user_id"])){
  header('Location: login.php');
  die();
}

try {
  $stmt = $db->prepare("SELECT * FROM v_resident WHERE `UserID` = ? LIMIT 1;");
  $stmt->bindParam(1, $_SESSION['user_id'], PDO::PARAM_INT);
  $stmt->execute();
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  $stmt = $db->prepare("SELECT * FROM room_stuff WHERE `RoomID` = ?;");
  $stmt->bindParam(1, $user['RoomID']);
  $stmt->execute();
  $stuffs = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
  echo "Error: {$e->getMessage()}";
  die();
}

?>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>ฟอร์มแจ้งซ่อม</title>

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
  <div style="display: flex; justify-content: center; margin-top: 16px;">
    <img src="logo.png" height="100">
  </div>

  <div class="brand">หอพักบุคลากร</div>
  <div class="address-bar">มหาวิทยาลัยอุบลราชธานี</div>

    <!-- Navigation -->
    <?php if ($_SESSION['user_type'] == 1): ?>
    <?php require 'user_1_nav.php'; ?>
    <?php endif ?>

    <?php if ($_SESSION['user_type'] == 3): ?>
    <?php require 'user_3_nav.php'; ?>
    <?php endif ?>

    <div class="container">
      <div class="box">
        <div class="panel panel-default">
          <div class="panel-body">
            <hr>
              <h2 class="intro-text text-center" style="color: #0080ff">ฟอร์มแจ้งซ่อม</h2>
            <hr>

            <div class="row" style="display: flex; justify-content: center;">
              <div class="col-md-6">

                <form action="form_repair_user_submit.php" method="post" 
                enctype="multipart/form-data" onsubmit="filevalidate(event, this);">

                  <div class="row">
                    <div class="form-group">
                      <label>แฟลต</label>
                      <input type="text" class="form-control" readonly value="<?= $user['Building'] ?>">
                    </div>
                  </div>

                  <div class="row">
                    <div class="form-group">
                      <label>ห้อง</label>
                      <input type="text" class="form-control" readonly value="<?= roomNumber($user['RoomID'], $db) ?>">
                    </div>
                  </div>

                  <div class="row">
                    <div class="form-group">
                      <label style="display: block;">วัสดุที่ต้องการซ่อม</label>
                    </div>
                  </div>
                  
                  <?php if (empty($stuffs)): ?>
                    <div class="row">
                      <p style="font-size: 1em; color: #777">ห้องนี้ยังไม่มีครุภัณฑ์ในระบบ</p>
                    </div>
                  <?php else: ?>
                  <?php foreach (array_chunk($stuffs, 3) as $chunk): ?>
                  <div class="row">
                    <div class="form-group">
                      <?php foreach ($chunk as $stuff): ?>     
                      <label class="checkbox-inline">
                        <input type="checkbox" name="item[]" value="<?= $stuff['Name'] ?>"> <?= $stuff['Name'] ?>
                      </label>
                      <?php endforeach ?>
                    </div>
                  </div>
                  <?php endforeach ?>
                  <?php endif ?>

                  <div class="row">
                    <div class="form-group">
                      <label>อื่น ๆ (โปรดระบุ)</label>
                      <input type="text" class="form-control" name="otheritem">
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="form-group">
                      <label>แนปรูป (สูงสุด 5 รูป)</label>
                      <input type="file" name="pic[]" accept="image/*" multiple>
                    </div>
                  </div>

                  <div class="row">
                    <button type="submit" class="btn btn-primary">แจ้งซ่อม</button>
                    <a href="index_member.php" class="btn btn-default">ยกเลิก</a>
                  </div>

                </form>

              </div>
            </div>


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

    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>

    <script type="text/javascript">
    <?php if ($_SESSION['user_type'] == 3): ?>
      $(document).on('ready', function () {
        fetchRoom(<?= $user['BuildingID'] ?>);
      });
    <?php endif ?>
    function filevalidate(e, form) {
      e.preventDefault();

      let checkboxs = $('input[type="checkbox"]:checked');

      if (checkboxs.length == 0 && $('input[name="otheritem"]').val() == "") {
        alert('กรุณาระบุของที่ต้องการซ่อม');
        return;
      }

      if (parseInt($("input[type='file']").get(0).files.length) > 5){
       alert("แนปรูปได้ไม่เกิน 5 รูป");
       return;
      }

      form.submit();

    }

    function fetchRoom(value) {
      var url = "getroom.php?id=" + value;

      $.get(url, function(response) {
        $('select[name="roomid"] option').remove();

        $('select[name="roomid"]').html(response);
      });
    }
    </script>

</body>

</html>
