<?php

require 'DBConnect.php';
session_start();
$user_id = $_SESSION['copy_from'] ? $_SESSION['copy_from'] : $_SESSION['user_id'];

$picPath = !file_exists(__DIR__ . '/userpic/' . $user_id . '.jpg') ? null : '/userpic/' . $user_id . '.jpg';

try {
    $stmt = $db->prepare("SELECT * FROM member WHERE UserID = :userid");
    $stmt->bindParam(':userid', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Error {$e->getMessage()}";
    die();
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

    <div class="brand">หอพักบุคลากร</div>
    <div class="address-bar">มหาวิทยาลัยอุบลราชธานี</div>

    <!-- Navigation -->
    <?php if ($_SESSION['user_type'] == 2 || $_SESSION['user_type'] == 3): ?>
      <?php require 'user_2_nav.php'; ?>
    <?php else: ?>
      <?php require 'user_'. $_SESSION['user_type'] .'_nav.php'; ?>
    <?php endif ?>

    <div class="container">



        <div class="box" style="background-color: #fff">
            <div class="row">
                <div class="col-lg-12">
                    <hr>
                    <h2 class="intro-text text-center">แก้ไขข้อมูลส่วนตัว
                    </h2>
                    <hr>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#profile" data-toggle="tab">ข้อมูลส่วนตัว</a>
                        </li>
                        <li>
                            <a href="#pic" data-toggle="tab">แก้ไขรูป</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane active" id="profile">
                            <div class="row">
                                <div class="col-md-6" style="padding: 20px;">
                                    <form action="update_user_profile.php" method="POST">
                                        <input type="hidden" name="userid" value="<?= $_SESSION['user_id'] ?>">
                                        <div class="form-group">
                                            <label for="userpnamet">คำนำหน้าชื่อ</label>
                                            <input type="text" class="form-control" name="userpnamet" required
                                            value="<?= $user['UserPNameT'] ?: '' ?>">
                                        </div>

                                        <div class="form-group">
                                            <label for="usernamet">ชื่อ</label>
                                            <input type="text" class="form-control" name="usernamet" required
                                            value="<?= $user['UserNameT'] ?: '' ?>">
                                        </div>

                                        <div class="form-group">
                                            <label for="usersnamet">นามสกุล</label>
                                            <input type="text" class="form-control" name="usersnamet" required
                                            value="<?= $user['UserSNameT'] ?: '' ?>">
                                        </div>

                                        <div class="form-group">
                                            <label for="usersnamet">โทรศัพท์</label>
                                            <input type="text" class="form-control" name="phone" required
                                            value="<?= $user['Phone'] ?: '' ?>">
                                        </div>

                                        <div class="form-group">
                                            <label for="usersnamet">E-mail</label>
                                            <input type="email" class="form-control" name="email" required
                                            value="<?= $user['Email'] ?: '' ?>">
                                        </div>

                                        <button type="submit" class="btn btn-primary">
                                            แก้ไขข้อมูลส่วนตัว
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="pic">
                            <div class="row">
                                <div class="col-md-6" style="padding: 20px;">
                                    <form action="upload_user_pic.php" method="POST" enctype="multipart/form-data">
                                        <?php if ($picPath): ?>
                                        <div class="form-group">
                                            <label>รูปปัจจุบัน</label> <br>
                                            <img src="<?= $picPath ?>">
                                        </div>
                                        <?php else: ?>
                                        <div class="form-group">
                                            <label>คุณยังไม่ได้อัพโหลดรูป</label> <br>
                                        </div>
                                        <?php endif ?>

                                        <div class="form-group">
                                            <label>อัพโหลดรูปใหม่ (รูปควรมีขนาด 160 x 160px)</label>
                                            <input type="file" name="newpic" accept="image/*">
                                        </div>

                                        <button type="submit" class="btn btn-primary">
                                            อัพโหลด
                                        </button>
                                    </form>
                                </div>
                            </div>
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

</body>

</html>
