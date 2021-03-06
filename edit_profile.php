<?php

require 'DBConnect.php';
session_start();
$user_id = $_SESSION['copy_from'] ? $_SESSION['copy_from'] : $_SESSION['user_id'];

$picPath = !file_exists(__DIR__ . '/userpic/' . $user_id . '.jpg') ? null : 'userpic/' . $user_id . '.jpg';

try {
    $stmt = $db->prepare("SELECT * FROM member WHERE UserID = :userid");
    $stmt->bindParam(':userid', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $db->prepare("SELECT * FROM addresses WHERE UserID = :userid LIMIT 1");
    $stmt->bindParam(':userid', $user_id);
    $stmt->execute();
    $address = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $db->prepare("SELECT * FROM province ORDER BY ProvinceNameT asc;");
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $provinces = $stmt->fetchAll();

    if ($address) {
        $stmt = $db->prepare("SELECT * FROM city WHERE ProvinceID = :province ORDER BY CityNameT asc;");
        $stmt->bindParam(':province', $address['ProvinceID']);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $cities = $stmt->fetchAll();

        $stmt = $db->prepare("SELECT * FROM district WHERE CityID = :city ORDER BY DistrictNameT asc;");
        $stmt->bindParam(':city', $address['CityID']);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $districts = $stmt->fetchAll();
    }

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
                                        <input type="text" class="form-control" name="userpnamet" required readonly
                                        value="<?= $user['UserPNameT'] ?: '' ?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="usernamet">ชื่อ</label>
                                        <input type="text" class="form-control" name="usernamet" required readonly
                                        value="<?= $user['UserNameT'] ?: '' ?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="usersnamet">นามสกุล</label>
                                        <input type="text" class="form-control" name="usersnamet" required readonly
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

                                    <div class="form-group">
                                        <label for="address">ที่อยู่</label>
                                        <textarea name="address" class="form-control" cols="30" rows="5" required><?= $address ? $address['Address'] : '' ?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="province">จังหวัด</label>
                                        <select name="province" class="form-control" onchange="fetchCity(this.value)" required>
                                            <option value="">--- จังหวัด ---</option>
                                            <?php foreach ($provinces as $province): ?>
                                                <option value="<?=$province['ProvinceID']?>"<?= $address && $address['ProvinceID'] == $province['ProvinceID'] ? ' selected' : '' ?>>
                                                    <?=$province['ProvinceNameT']?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <?php if (!$address): ?>
                                    <div class="form-group">
                                        <label for="city">อำเภอ</label>
                                        <select name="city" class="form-control" onchange="fetchDistrict(this.value)" required>
                                            <option value="">--- โปรดเลือกจังหวัด ---</option>
                                        </select>
                                    </div>
                                    <?php else: ?>
                                    <div class="form-group">
                                        <label for="city">อำเภอ</label>
                                        <select name="city" class="form-control" onchange="fetchDistrict(this.value)" required>
                                            <?php foreach ($cities as $city): ?>
                                                <option value="<?=$city['CityID']?>"<?= $address && $address['CityID'] == $city['CityID'] ? ' selected' : '' ?>>
                                                    <?=$city['CityNameT']?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <?php endif ?>  
                                    
                                    <?php if (!$address): ?>
                                    <div class="form-group">
                                        <label for="district">ตำบล</label>
                                        <select name="district" class="form-control" required>
                                            <option value="">--- โปรดเลือกอำเภอ ---</option>
                                        </select>
                                    </div>
                                    <?php else: ?>
                                    <div class="form-group">
                                        <label for="district">ตำบล</label>
                                        <select name="district" class="form-control" required>
                                            <?php foreach ($districts as $district): ?>
                                                <option value="<?=$district['DistrictID']?>"<?= $address && $address['DistrictID'] == $district['DistrictID'] ? ' selected' : '' ?>>
                                                    <?=$district['DistrictNameT']?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <?php endif ?>

                                    <div class="form-group">
                                        <label for="zip">รหัสไปรษณีย์</label>
                                        <input type="text" class="form-control" name="zip" value="<?= $address['Zip'] ?>" required>
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

<script>
function fetchCity(value) {
    var url = "getcity.php?id=" + value;

    $.get(url, function(response) {
        $('select[name="city"] option').remove();
        $('select[name="city"]').html(response).change();
    });
}

function fetchDistrict(value) {
    var url = "getdistrict.php?id=" + value;

    $.get(url, function(response) {
        $('select[name="district"] option').remove();
        $('select[name="district"]').html(response);
    });
}
</script>
</body>

</html>
