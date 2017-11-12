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
  $stmt = $db->prepare("SELECT * FROM building ");
  $stmt->execute();
  $stmt->setFetchMode(PDO::FETCH_ASSOC);
  $result = $stmt->fetchAll();
}
catch(PDOException $e) {
  echo "Error: " . $e->getMessage();
}
$conn = null;
 ?>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>การเพิ่ม Username</title>

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
                      <a href="index_admin.php">หน้าหลัก</a>
                  </li>
                  <li>
                      <a href="building.php">จัดการอาคารที่พัก</a>
                  </li>
                  <li>
                      <a href="form_handle.php">จัดการแบบฟอร์ม</a>
                  </li>
                  <li>
                      <a href="member.php">จัดการสมาชิก</a>
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

        <div class="row">
            <div class="box">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="table-responsive">
                                  <div class="row">
                                    <div class="col-lg-12">
                                  <hr>
                                  <font color ="#0080ff"><h2 class="intro-text text-center">การเพิ่ม Username</h2></font>
                                  <hr>
                                   </div>
                                      <div class="col-lg-6">

                                          <form role="form" action= "building_insert.php" method="post">

                                              <div class="form-group">
                                                  <label>เพิ่ม Username</label>
                                                  <input class="form-control" name= "user1">
                                              </div>

                                              <div class="form-group">
                                                  <label>เพิ่ม Password</label>
                                                  <input class="form-control" name="pass1">
                                              </div>

                                              <div class="form-group">
                                                  <label>เลือกประเภท User</label>
                                                  <select class="form-control" name= "roomtype1">
                                                    <option value="1">ผู้พักอาศัย</option>
                                                    <option value="2">อนุกรรมการที่พักอาศัย</option>
                                                    <option value="3">คณะกรรมการที่พักอาศัย</option>
                                                    <option value="9">ผู้ดูแลระบบ</option>
                                                  </select>
                                              </div>


                                              <button type="submit" class="btn btn-default">ตกลง</button>
                                              <button type="reset" class="btn btn-default">ยกเลิก</button>
                                                <br></br>


                                </table>
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

    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>

    <script type="text/javascript">
    $(document).ready(function() {
        $('#building').DataTable();
    } );

    function changevalue(value) {
      $('input[name=id]').val(value);
    }
    </script>

</body>

</html>
