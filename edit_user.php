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
  $stmt = $db->prepare('SELECT * FROM `member` WHERE `UserID` = :userid OR `CopyFrom` = :userid2');
  $stmt->bindParam(':userid', $_GET['id']);
  $stmt->bindParam(':userid2', $_GET['id']);
  $stmt->execute();
  $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
  echo "Error: " . $e->getMessage();
}

try {
  $stmt = $db->prepare("SELECT * FROM allvars WHERE FieldName = 'UserType'");
  $stmt->execute();
  $stmt->setFetchMode(PDO::FETCH_ASSOC);
  $userTypes = $stmt->fetchAll();
} catch(PDOException $e) {
  echo "Error: " . $e->getMessage();
}

function getUserTypeName($id) {
  global $userTypes;

  return array_values(array_filter($userTypes, function ($type) use ($id) {
        return $type["FieldCode"] == $id;
    }))[0]['ValueT'];
}

 ?>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>จัดการสมาชิก | แก้ไข User</title>

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
      <div class="box">
        <div class="col-lg-12">
          <hr>
          <h2 class="intro-text text-center" style="color: #0080ff">แก้ไข User สมาชิก<br><br> <a href="member.php">กลับ</a></h2>
          <hr>
        </div>

        <div class="row">
          <div class="col-lg-12">
            <h4>User ทั้งหมดของ<?= $users[0]['UserPNameT'] . ' ' . $users[0]['UserNameT'] .' '. $users[0]['UserSNameT'] ?></h4>
            <table class="table table-bordered">
              <thead>
                <th>Username</th>
                <th>ประเภท</th>
                <th>แก้ไข</th>
              </thead>
              <tbody>
              <?php foreach ($users as $user): ?>
                <tr>
                  <td><?= $user['UserLogin'] ?> <?= !$user['CopyFrom'] ? '(หลัก)' : '' ?></td>
                  <td><?= getUserTypeName($user['UserType']) ?></td>
                  <td>
                    <button class="btn btn-primary" onclick="showModal(event, <?= $user['UserID'] ?>)">แก้ไข</button>
                    <?php if ($user['CopyFrom']): ?>
                    <form action="delete_user.php" method="POST" style="display: inline;">
                      <input type="hidden" name="UserID" value="<?= $user['UserID'] ?>">
                      <button type="submit" class="btn btn-danger">ลบ</button>
                    </form>
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
            <h4 class="modal-title">แก้ไข Username และ Password</h4>
          </div>
          <div class="modal-body" id="modalContent">
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
    function showModal(event, id) {
      event.preventDefault();
      
      var url = "get_user_edit_form.php?id=" + id;

      $.get(url, function(res) {
        $('#modalContent').html('');
        $('#modalContent').html(res);

        $('#modal').modal('show');
      });
    }
    </script>

</body>

</html>
