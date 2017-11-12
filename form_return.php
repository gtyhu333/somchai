<?php
require 'DBconnect.php';

session_start();

try {
  $stmt = $db->prepare("SELECT * FROM v_resident WHERE UserID = ?");
  $stmt->bindParam(1, $_SESSION['user_id'], PDO::PARAM_INT);
  $stmt->execute();
  $user = $stmt->fetch(PDO::FETCH_ASSOC);
  // dd($user);

  $stmt = $db->prepare("SELECT * FROM building ");
  $stmt->execute();
  $stmt->setFetchMode(PDO::FETCH_ASSOC);
  $result = $stmt->fetchAll();

  $stmt = $db->prepare("SELECT * FROM position;");
  $stmt->execute();
  $stmt->setFetchMode(PDO::FETCH_ASSOC);
  $positions = $stmt->fetchAll();

  $stmt = $db->prepare("SELECT * FROM faculty;");
  $stmt->execute();
  $stmt->setFetchMode(PDO::FETCH_ASSOC);
  $faculty = $stmt->fetchAll();

  $stmt = $db->prepare("SELECT * FROM province ORDER BY ProvinceNameT asc;");
  $stmt->execute();
  $stmt->setFetchMode(PDO::FETCH_ASSOC);
  $provinces = $stmt->fetchAll();

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

    <title>หน้าของสมาชิกผู้พักอาศัย</title>

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
                        <a href="form_return.php"><b>แบบฟอร์มขอส่งคืนห้องพัก</b></a>
                    </li>
                    <li>
                        <a href="form_switch.php"><b>แบบฟอร์มขอสลับห้องพัก</b></a>
                    </li>
                    <li>
                        <a href="form_repair_member.php"><b>การแจ้งซ่อม</b></a>
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



      <div class="col-lg-12">
          <div class="panel panel-default" style="padding: 0 3rem">
            <div class="modal-header">
              <div style="display: flex; justify-content: center; margin-top: 16px;">
                <img src="logo.png" height="100">
              </div>
            <h2 class="text-center">แบบฟอร์มขอส่งคืนที่พักอาศัยมหาวิทยาลัยอุบลราชธานี</h2>
            </div>
          <div class="modal-body">
              <form action="form_return_handle.php" method="post">

            </div>
            <label>ส่วน : ผู้พักอาศัย </label>
            <br></br>
            <div>

              <div class="row">
                <div class="form-group col-sm-3">
                  <label>ชื่อ-สกุล</label>
                  <input type="text" class="form-control" readonly
                  value="<?= $user['Name'] ?>">
                  <input type="hidden" name="userid" value="<?= $user['UserID'] ?>">
                  <input type="hidden" name="residentid" value="<?= $user['ResidentID'] ?>">
                </div>
              </div>

               <div class="row" style="margin-top: 2rem">
                 <div class="form-group col-sm-3">
                     <label>คณะ</label>
                     <input type="text" class="form-control" readonly
                      value="<?= $user['Faculty'] ?>">
                 </div>
               </div>

                <div class="row" style="margin-top: 2rem">
                  <div class="form-group col-sm-3">
                      <label>ตำแหน่ง</label>
                      <input type="text" class="form-control" readonly
                      value="<?= $user['Postion'] ?>">
                  </div>
                </div>
<hr>
                   <div class="row">
                     <div class="form-group col-sm-3">
                   <label>ผู้พักอาศัยห้องพักหมายเลข</label>
                   <input class="form-control" name="room_number" readonly
                   value="<?= roomNumber($user['RoomID'], $db) ?>">
                 </div>
                   </div>

                   <div class="row">
                     <div class="form-group col-sm-3">
                   <label>แฟลต</label>
                   <input class="form-control" name="flat" readonly
                   value="<?= $user['Building'] ?>">
                   <input type="hidden" name="buildingid" value="<?= $user['BuildingID'] ?>">
                 </div>
                   </div>


                <label>วันที่ขอส่งคืนที่พักอาศัย</label>
                <div class="form-inline" style="margin-top: 2rem">
                  <div class="form-group">

                      <label> วันที่</label>

                      <select class="form-control" name="day">
                        <?PHP for($i=1; $i<=31; $i++)
                        {?>
                          <option value="<?PHP echo str_pad($i, 2, '0', STR_PAD_LEFT);?>"><?PHP echo $i?></option>
                  <?PHP } ?>
                  </select>
                </div>

                      <label>เดือน</label>
                      <select  class="form-control" name="month">
                        <?PHP $month = array("มกราคม ", "กุมภาพันธ์ ", "มีนาคม ", "เมษายน ", "พฤษภาคม ", "มิถุนายน ", "กรกฎาคม ", "สิงหาคม ", "กันยายน ", "ตุลาคม ", "พฤศจิกายน ", "ธันวาคม "); ?>
                        <?PHP for($k=0; $k<sizeof($month); $k++) {?>
                          <option value="<?PHP echo str_pad($k+1, 2, '0', STR_PAD_LEFT);?>" > <?PHP echo $month[$k]?></option>
                            <?PHP } ?>
                          </select>

                      <label>ปี พ.ศ. </label>
                      <select class="form-control" name="year">
                        <?PHP for($j=2500; $j<=2600; $j++)
                        {?>
                          <option value="<?PHP echo $j - 543?>"><?PHP echo $j?></option>
                  <?PHP } ?>


                  </select>
                        <br></br>
                  </div>

                  <div class="row">
                    <div class="form-group col-sm-5">
                  <label>เหตุผลในการขอส่งคืนห้องพัก</label>
                  <textarea class="form-control" rows="5" name="cause"></textarea>
                </div>
                  </div>

                  <button type="submit" class="btn btn-primary">ตกลง</button>
                  <button type="reset" class="btn btn-default">ยกเลิก</button>
                </div>




                <br></br>
              </form>
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

    <!-- Script to Activate the Carousel -->
    <script>
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
