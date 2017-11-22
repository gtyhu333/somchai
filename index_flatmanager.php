<!DOCTYPE html>
<html lang="en">
<?php
require 'DBConnect.php';

session_start();
if (!isset($_SESSION["user_id"])){
  header('Location: login.php');
  die();
}

if ($_SESSION['user_type'] != 5) {
    session_destroy();
  header('Location: login.php');
  die();
}

$user_id = $_SESSION['copy_from'] ? $_SESSION['copy_from'] : $_SESSION['user_id'];
$picPath = !file_exists(__DIR__ . '/userpic/' . $user_id . '.jpg') ? null : 'userpic/' . $user_id . '.jpg';

try {
    $stmt = $db->prepare("SELECT * FROM member WHERE UserID = :userid;");
    $stmt->bindParam(':userid', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $db->prepare("
        SELECT 1 FROM payments WHERE ResidentID = :residentid AND Month = :month 
        AND Year = :year
    ");
    $thisMonth = (int) date("n");
    $thisYear = (int) date("Y");
    $stmt->bindParam(':residentid', $user['ResidentID']);
    $stmt->bindParam(':month', $thisMonth);
    $stmt->bindParam(':year', $thisYear);
    $stmt->execute();
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
    
    if ($needPayment = empty($payments)) {
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, true); // fixes 'packet ouf of order bug'
        $stmt = $db->prepare("CALL getMonthly(:roomid)");
        $stmt->bindParam(':roomid', $user['RoomID']);
        $stmt->execute();
        $bills = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        if ($bills) {
            array_walk($bills, function (&$value, $key) {
                return [$key => number_format($value)];
            });
        }
    }

    // dd($bills);

} catch (Exception $e) {
    echo "Error {$e->getMessage()}";
    die();
}
?>
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>หน้าของคณะกรรมการสวัสดิการที่พักอาศัย</title>

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
    <?php require 'user_5_nav.php'; ?>

    <div class="container">



        <div class="row">
            <div class="box">
                <div class="col-lg-12">
                    <hr>
                    <h2 class="intro-text text-center">ข้อมูลส่วนตัว
                    </h2>
                    <h3 class="intro-text text-center"><?=  $user['UserPNameT'] . $user['UserNameT'] . ' ' . $user['UserSNameT'] ?></h3>
                    <img src="<?= $picPath ?>" style="display: block; margin: 0 auto; height: 130px;">
                    <a class="text-center" href="edit_profile.php" style="display: block;"><b>แก้ไขข้อมูลส่วนตัว</b></a>
                    <hr>
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
    $('.carousel').carousel({
        interval: 5000 //changes the speed
    })
    </script>

</body>

</html>
