<?php
require 'DBconnect.php';

session_start();
if (!isset($_SESSION["user_id"])){
  header('Location: login.php');
  die();
}

if (!in_array($_SESSION['user_type'], [9])) {
  header('Location: login.php');
  die();
}

$stmt = $db->prepare("SELECT * FROM province ORDER BY ProvinceNameT asc;");
$stmt->execute();
$stmt->setFetchMode(PDO::FETCH_ASSOC);
$provinces = $stmt->fetchAll();

function getExpenseType()
{
  global $db;
  $stmt = $db->prepare("SELECT * FROM expensetype");
  $stmt->execute();
  $stmt->setFetchMode(PDO::FETCH_ASSOC);
  return $stmt->fetchAll();
}

$months = [
  '', "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"
];
?>
<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>พิมพ์ใบจ่ายเงิน</title>

  <!-- Bootstrap Core CSS -->
  <link href="css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom CSS -->
  <link href="css/business-casual.css?1" rel="stylesheet">

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
        <?php elseif ($_SESSION['user_type'] == 9): ?>
          <?php require 'user_'. $_SESSION['user_type'] .'_nav.php'; ?>
        <?php endif ?>

        <div class="container">

          <div class="box" style="background-color: #fff">
            <div class="row">
              <div class="col-lg-12">
                <hr>
                <h2 class="intro-text text-center">พิมพ์ใบจ่ายเงิน</h2>
                <hr>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-12">
                <form action="testpdfprint.php" method="POST">

                  <div>
                    <label>วันที่รับเงิน</label>
                  </div>
                  <div class="form-inline">
                    <div class="form-group">
                      <select name="day" class="form-control" required>
                        <?php foreach (range(1, 31) as $day): ?>
                          <option value="<?= $day ?>"><?= $day ?></option>
                        <?php endforeach ?>
                      </select>
                    </div>
                    <div class="form-group">
                      <select name="month" class="form-control" required>
                        <?php foreach ($months as $index => $month): ?>
                          <?php if ($index === 0): ?>
                            <?php continue; ?>
                          <?php endif ?>
                          <option value="<?= $month ?>"><?= $month ?></option>
                        <?php endforeach ?>
                      </select>
                    </div>
                    <div class="form-group">
                      <select name="year" class="form-control" required>
                        <?php foreach (range(2559, 2569) as $year): ?>
                          <option value="<?= $year ?>"><?= $year ?></option>
                        <?php endforeach ?>
                      </select>
                    </div>
                  </div>

                  <div class="form-group" style="margin-top: 1rem;">
                    <div class="row">
                      <div class="col-lg-5">
                        <label for="name">ชื่อผู้รับเงิน</label>
                        <input type="text" class="form-control" name="receiptname" required>
                      </div>
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="row">
                      <div class="col-lg-5">
                        <label for="name">ที่อยู่ผู้รับเงิน</label>
                        <input type="text" class="form-control" name="street">
                      </div>
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="row">
                      <div class="col-lg-5">
                        <label for="name">จังหวัด</label>
                        <select class="form-control" name="province" onchange="fetchcity(this.value);">
                          <option value="">---- โปรดเลือกจังหวัด ----</option>
                          <?php foreach ($provinces as $province): ?>
                            <option value="<?=$province['ProvinceID']?>"><?=$province['ProvinceNameT']?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="row">
                      <div class="col-lg-5">
                        <label for="name">อำเภอ</label>
                        <select class="form-control" name="amphoe" onchange="fetchdistrict(this.value);">
                          <option value="">---- โปรดเลือกจังหวัด ----</option>
                        </select>
                      </div>
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="row">
                      <div class="col-lg-5">
                        <label for="name">ตำบล</label>
                        <select class="form-control" name="tambon">
                          <option value="">---- โปรดเลือกอำเภอ ----</option>
                        </select>
                      </div>
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="row">
                      <div class="col-lg-5">
                        <label for="name">รหัสไปรษณีย์</label>
                        <input type="text" class="form-control" name="zip">
                      </div>
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="row">
                      <div class="col-lg-5">
                        <label for="name">โทรศัพท์</label>
                        <input type="text" class="form-control" name="phone">
                      </div>
                    </div>
                  </div>

                  <div style="margin-top: 1rem">
                    <label>รายการ</label>
                  </div>

                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th>ชื่อ</th>
                        <th>จำนวน (บาท)</th>
                        <th>เพิ่มลบ</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>
                          <input type="text" class="form-control" list="list1" name="name[]" style="width: 90%"
                          oninput="getPrice(this)" required>
                        </td>
                        <td class="pricetr">
                          <input type="text" class="form-control" value="" name="price[]" style="width: 90%" required>
                        </td>
                        <td>
                          <button type="button" class="btn btn-default" 
                          onclick="clone(this.parentNode.parentNode)">+</button>

                          <button type="button" class="btn btn-default delbtn" 
                          onclick="del(this.parentNode.parentNode)"
                          disabled="true">-</button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  
                  <div class="form-group">
                    <div class="row">
                      <div class="col-lg-5">
                        <button type="submit" class="btn btn-primary">พิมพ์ใบจ่ายเงิน</button>
                      </div>
                    </div>
                  </div>

                  <datalist id="list1">
                    <?php foreach (getExpenseType() as $type): ?>
                      <option value="<?= $type['ExpenseName'] ?>">
                        <?= $type['ExpenseName'] ?>
                      </option>
                    <?php endforeach ?>
                  </datalist>
            
                </form>
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
          var rowCount = 1;

          function getPrice(element) {
            let value = element.value;
            let input = element.parentNode.parentNode.querySelector(".pricetr > input");

            $.get("get_expense_price.php", { name: value }, function (response) {
              let result = JSON.parse(response).DefaultRate;

              if (result != 0) {
                input.value = JSON.parse(response).DefaultRate;
              }
            });
          }

          function clone(row) {
            row.parentNode.appendChild(row.cloneNode(true));

            if (++rowCount > 1) {
              document.querySelectorAll("button.delbtn").forEach(function(input) {
                input.disabled = false;
              });
            }
          }

          function del(row) {
            row.parentNode.removeChild(row);

            if (--rowCount == 1) {
              document.querySelectorAll("button.delbtn").forEach(function(input) {
                input.disabled = true;
              });
            }
          }

          function fetchcity(value) {
            if (value == "") { return; }
            var url = "getcity.php?id=" + value;

            $.get(url, function(response) {
              $('select[name="amphoe"] option').remove();

              $('select[name="amphoe"]').html(response).change();
            });
          }

          function fetchdistrict(value) {
            var url = "getdistrict.php?id=" + value;

            $.get(url, function(response) {
              $('select[name="tambon"] option').remove();

              $('select[name="tambon"]').html(response);
            });
          }
        </script>

      </body>

      </html>
