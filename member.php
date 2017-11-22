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
  $sql = "SELECT * FROM v_resident WHERE Name IS NOT NULL";
  if (isset($_GET['building']) && $_GET['building'] != 'all') {
    $sql .= " AND BuildingID = {$_GET['building']}";
  }
  $stmt = $db->prepare($sql);
  $stmt->execute();
  $stmt->setFetchMode(PDO::FETCH_ASSOC);
  $residents = $stmt->fetchAll();
} catch(PDOException $e) {
  echo "Error: " . $e->getMessage();
}

try {
  $stmt = $db->prepare("SELECT * FROM allvars WHERE FieldName = 'UserType'
    AND FieldCode IN (1, 2, 5)");
  $stmt->execute();
  $stmt->setFetchMode(PDO::FETCH_ASSOC);
  $userTypes = $stmt->fetchAll();
} catch(PDOException $e) {
  echo "Error: " . $e->getMessage();
}

try {
  $stmt = $db->prepare("SELECT * FROM building");
  $stmt->execute();
  $stmt->setFetchMode(PDO::FETCH_ASSOC);
  $buildings = $stmt->fetchAll();
} catch(PDOException $e) {
  echo "Error: " . $e->getMessage();
}

try {
  $stmt = $db->prepare("SELECT * FROM faculty");
  $stmt->execute();
  $stmt->setFetchMode(PDO::FETCH_ASSOC);
  $faculties = $stmt->fetchAll();
} catch(PDOException $e) {
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

    <title>จัดการสมาชิก</title>

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
    <?php require 'admin_nav.php'; ?>

    <div class="container">

        <div class="row">
            <div class="box" style="background-color: #fff; padding: 15px;">

                <div class="row">
                    <div class="col-lg-12">
                        <div class="">
                            <div class="">
                                <div class="table-responsive">

                                          <div class="col-lg-12">
                                              <hr>
                                              <font color ="#0080ff"><h2 class="intro-text text-center">การจัดการสมาชิก</h2></font>
                                              <hr>
                                          </div>

                                            <form action="member.php" method="get" id="buildingform">
                                            <div class="form-group col-lg-12">
                                              <label>ชื่ออาคาร</label>
                                              <div class="row">
                                                <div class="col-md-6">
                                                  <select class="form-control" name="building" onchange="document.getElementById('buildingform').submit()">
                                                    <option value="all"<?= ! isset($_GET['building']) ? ' selected' : '' ?>>แฟลตทั้งหมด</option>
                                                    <?php foreach ($buildings as $building): ?>
                                                      <option value="<?= $building['BuildingID'] ?>"<?= isset($_GET['building']) &&  $_GET['building'] == $building['BuildingID'] ? ' selected' : '' ?>>
                                                        <?= $building['BuildingName'] ?>
                                                      </option>
                                                    <?php endforeach ?>
                                                  </select>

                                                </div>

                                                </div>
                                              </div>
                                              </form>
                                            </div>
                                          </div>
                                        <div class="panel-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover table-striped" id="room">
                                                    <thead>
                                                      <th><center>แฟลต</center></th>
                                                      <th><center>ห้อง</center></th>
                                                      <th><center>ชื่อ - สกุล</center></th>
                                                      <th><center>ประเภท</center></th>
                                                      <th><center>ตำแหน่ง</center></th>
                                                      <th><center>สังกัด</center></th>
                                                      <th><center>เพิ่ม / แก้ไข Username</center></th>
                                                      <th><center>แก้ไขข้อมูลสมาชิก<center></th>
                                                      <th><center>ลบ<center></th>
                                                    </thead>
                                                      <tbody>
                                                      <?php foreach ($residents as $resident) : ?>
                                                        <tr>
                                                          <?php $usertype =  memberType($resident['UserID'], $db) ?>
                                                          <td><center><?= $resident['Building'] ?></center></td>
                                                          <td><center><?= roomNumber($resident['RoomID'], $db) ?></center></td>
                                                          <td><center><?= $resident['Name'] ?></center></td>
                                                          <td><center><?= $usertype[1] ?></center></td>
                                                          <td><center><?= $resident['Postion'] ?></center></td>
                                                          <td><center><?= $resident['Faculty'] ?></center></td>

                                                          <td><center>
                                                              <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#modal<?= $resident['UserID'] ?>">
                                                                เพิ่ม / แก้ไข
                                                              </a>
                                                          </center></td>

                                                          <td><center>
                                                              <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#modalEdit<?= $resident['UserID'] ?>">
                                                                แก้ไข
                                                              </a>
                                                          </center></td>

                                                        <td>  <center>
                                                            <form action="form_handle_delete.php" method="post">
                                                              <input type="hidden" name="StaffID" value="<?= $resident['UserID']?>">
                                                              <button type="submit" class="btn btn-danger" name="button">ลบ</button>
                                                            </form>
                                                          </certer></td>

                                                        </tr>

                                                        <!-- Modal -->
                                                        <div id="modal<?= $resident['UserID'] ?>" class="modal fade" role="dialog">
                                                          <div class="modal-dialog">

                                                            <!-- Modal content-->
                                                            <div class="modal-content">
                                                              <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                <h4 class="modal-title">เพิ่ม / แก้ไข Username และ Password</h4>
                                                              </div>
                                                              <div class="modal-body">
                                                                <ul class="nav nav-tabs">
                                                                  <?php if ($resident['UserLogin']): ?>
                                                                  <li class="<?= $resident['UserLogin'] ? 'active' : '' ?>">
                                                                    <a href="#edit<?= $resident['UserID'] ?>" aria-controls="edit<?= $resident['UserID'] ?>" data-toggle="tab">แก้ไข</a>
                                                                  </li>
                                                                  <?php endif ?>

                                                                  <li class="<?= !$resident['UserLogin'] ? 'active' : '' ?>">
                                                                    <a href="#add<?= $resident['UserID'] ?>" aria-controls="add<?= $resident['UserID'] ?>" data-toggle="tab">เพิ่ม</a>
                                                                  </li>
                                                                </ul>

                                                                <div class="tab-content">
                                                                  <?php if ($resident['UserLogin'] ): ?>
                                                                  <div class="tab-pane<?= $resident['UserLogin'] ? ' active' : '' ?>" id="edit<?= $resident['UserID'] ?>" style="padding: 20px">
                                                                    <a href="edit_user.php?id=<?= $resident['UserID'] ?>">คลิกที่นี่เพื่อไปยังหน้าแก้ไข User</a>
                                                                  </div>
                                                                  <?php endif ?>

                                                                  <div class="tab-pane<?= !$resident['UserLogin'] ? ' active' : '' ?>" id="add<?= $resident['UserID'] ?>">
                                                                    <form action="add_new_user.php" method="post">
                                                                      <div class="form-group">
                                                                        <label for="username">Username</label>
                                                                        <input type="text" class="form-control" name="username" placeholder="Username" required>
                                                                      </div>

                                                                      <div class="form-group">
                                                                        <label for="username">Password</label>
                                                                        <input type="password" class="form-control" name="password" placeholder="Password" required>
                                                                      </div>

                                                                      <div class="form-group">
                                                                        <label for="usertype">ประเภทสมาชิก</label>
                                                                        <select name="usertype" class="form-control" required>
                                                                          <option value="">โปรดเลือก</option>
                                                                          <?php foreach ($userTypes as $type): ?>
                                                                            <option value="<?= $type['FieldCode'] ?>"<?= $usertype[0] == $type['FieldCode'] ? ' selected' : '' ?>>
                                                                              <?= $type['ValueT'] ?>
                                                                            </option>
                                                                          <?php endforeach ?>
                                                                        </select>
                                                                      </div>

                                                                      <input type="hidden" name="userid" value="<?= $resident['UserID'] ?>">

                                                                      <button type="submit" class="btn btn-primary">เพิ่ม</button>
                                                                      <button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
                                                                    </form>
                                                                  </div>
                                                                </div>
                                                                
                                                              </div>
                                                            </div>

                                                          </div>
                                                        </div>

                                                        <div id="modalEdit<?= $resident['UserID'] ?>" class="modal fade" role="dialog">
                                                          <div class="modal-dialog">

                                                            <!-- Modal content-->
                                                            <div class="modal-content">
                                                              <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                <h4 class="modal-title">แก้ไขข้อมูลสมาชิก</h4>
                                                              </div>
                                                              <div class="modal-body">
                                                                <form action="update_user_alt.php" method="post">
                                                                <?php $userinfo = getUserInfo($resident['UserID'], $db) ?>
                                                                  <div class="form-group">
                                                                    <label for="userpnamet">คำนำหน้าชื่อ</label>
                                                                    <input type="text" class="form-control" name="userpnamet" required
                                                                    value="<?= $userinfo['UserPNameT'] ?: '' ?>">
                                                                  </div>

                                                                  <div class="form-group">
                                                                    <label for="usernamet">ชื่อ</label>
                                                                    <input type="text" class="form-control" name="usernamet" required
                                                                    value="<?= $userinfo['UserNameT'] ?: '' ?>">
                                                                  </div>

                                                                  <div class="form-group">
                                                                    <label for="usersnamet">นามสกุล</label>
                                                                    <input type="text" class="form-control" name="usersnamet" required
                                                                    value="<?= $userinfo['UserSNameT'] ?: '' ?>">
                                                                  </div>

                                                                  <div class="form-group">
                                                                    <label for="usertype">สังกัด / คณะ</label>
                                                                    <select name="facid" class="form-control" required onchange="selectChange(this.value, 'dept<?= $resident['UserID'] ?>')">
                                                                      <option value="">โปรดเลือก</option>
                                                                      <?php foreach ($faculties as $faculty): ?>
                                                                        <option value="<?= $faculty['FacID'] ?>"<?= $resident['FacID'] == $faculty['FacID'] ? ' selected' : '' ?>>
                                                                          <?= $faculty['FacNameT'] ?>
                                                                        </option>
                                                                      <?php endforeach ?>
                                                                    </select>
                                                                  </div>

                                                                  <div class="form-group">
                                                                    <label for="usertype">ภาควิชา / แผนก</label>
                                                                    <select name="deptid" class="form-control" id="dept<?= $resident['UserID'] ?>" required>
                                                                      <option value="">โปรดเลือก</option>
                                                                      <?php foreach (getDepartments($resident['FacID'], $db) as $department): ?>
                                                                        <option value="<?= $department['DeptID'] ?>"<?= $resident['DeptID'] == $department['DeptID'] ? ' selected' : '' ?>>
                                                                          <?= $department['DeptNameT'] ?>
                                                                        </option>
                                                                      <?php endforeach ?>
                                                                    </select>
                                                                  </div>

                                                                  <input type="hidden" name="userid" value="<?= $resident['UserID'] ?>">

                                                                  <button type="submit" class="btn btn-primary">แก้ไข</button>
                                                                  <button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
                                                                </form>
                                                              </div>
                                                            </div>

                                                          </div>
                                                        </div>
                                                      <?php endforeach; ?>
                                                    </tbody>
                                                </table>

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
        $('input[name=id]').val($('#selectid').val());
    } );

    function selectChange(value, id) {
      var url = "getdept.php?id=" + value;

      $.get(url, function(response) {
        $('#' + id + ' option').remove();

        $('#' + id).html(response);
      });
    }
    </script>

</body>

</html>
