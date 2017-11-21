<?php

require 'DBconnect.php';

$range = [
  '1' => [date('Y') . '-01-01', date('Y') . '-04-01'],
  '2' => [date('Y') . '-04-02', date('Y') . '-08-01'],
  '3' => [date('Y') . '-08-02', date('Y') . '-12-31'],
];

try {
  $stmt = $db->prepare("SELECT * FROM news ORDER BY date DESC");
  $stmt->execute();
  $news = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

  <title>index main</title>

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
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <ul class="nav navbar-nav">
                <li>
                  <a href="index.php">หน้าหลัก</a>
                </li>
                <li>
                 <a href="form_request.php">  <u><b>แบบฟอร์มขอเข้าพัก</b></u></a>
               </li>
               <li>
                <a href="building_normal.php">สถานะหอพักบุคลากร</a>
              </li>
              <li>
                <a href="login.php">เข้าสู่ระบบ</a>
              </li>
            </ul>
          </div>
          <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
      </nav>

      <div class="container">
        <div class="box" style="background-color: #fff">
          <div class="row">
            <div class="col-lg-12">
              <h4 class="text-center" style="color: hsl(211, 96%, 54%)">
                ประกาศข่าวสารทั้งหมดจากหอพักบุคลากรมหาวิทยาลัยอุบลราชธานี
              </h4>
            </div>
          </div>

          <?php foreach ($news as $new): ?>
          <div class="row">
            <div class="col-lg-12">
              <div class="panel panel-default">
                <div class="panel-heading">
                  <h3 class="panel-title">
                    <?= $new['title'] ?> <br>
                    <small>
                      <?= sqlDateToThaiDate($new['date']) . ' ' . date('H:i:s', strtotime($new['date'])) ?>
                    </small>
                  </h3>
                </div>
                <div class="panel-body">
                  <?= $new['content'] ?>
                </div>
              </div>
            </div>
          </div>
          <?php endforeach ?>

          <div class="row">
            <div class="col-lg-12 text-center">
              <a href="index.php" class="btn btn-default">กลับหน้าหลัก</a>
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
