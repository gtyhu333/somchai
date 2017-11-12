<?php
require 'DBconnect.php';

try {
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
$conn = null;
 ?>
<!DOCTYPE html>
<html lang="en">


<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>แบบฟอร์มขอเข้าพักอาศัย</title>

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

        <div class="row">
            <div class="box">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="table-responsive">

                                      <div class="col-lg-12">

                                        <div class="modal-header">
                                          <div style="display: flex; justify-content: center; margin-top: 2px;">
                                            <img src="logo.png" height="75">
                                          </div>
                                        <h3 class="text-center">แบบฟอร์มขอรับการจัดสรรที่พักอาศัย มหาวิทยาลัยอุบลราชธานี</h3>
                                        </div>

                                        <form action="form_test.php" method="post">
                                          <br>
                                        <label><font color ="blue">ส่วนที่ 1 : ผู้ขอรับการจัดสรร </font> </label>
                                        <br></br>

                                              <div class="form-inline">
                                            <label>เลขประจำตัวประชาชน <font color ="red">*</font> </label>
                                            <input style="width:200px" class="form-control" maxlength ="13" name="p_id">
                                          </div>

                                            <br>

                                           <div class="form-inline">
                                             <div class="form-group">
                                             <label>คำนำหน้าชื่อ <font color ="red">*</font> </label>
                                             <input style="width:100px" class="form-control" maxlength = "20" name="pname">
                                           </div>
                                           &nbsp;
                                             <div class="form-group">
                                             <label>ชื่อ <font color ="red">*</font></label>
                                             <input style="width:150px" class="form-control" maxlength = "30" name="name">
                                           </div>
                                            &nbsp;
                                           <div class="form-group">
                                             <label>นามสกุล <font color ="red">*</font> </label>
                                             <input style="width:150px"class="form-control" maxlength = "30" name="surname">
                                              </div>
                                           </div>
                                           <br>
                                              <label>สถานะผู้สมัคร <font color ="red">*</font> </label>
                                           <div class="form-group">
                                               <div class="radio">
                                                   <label>
                                                       <input type="radio" name="optionsRadios" id="optionsRadios1" value="option1" checked>ไม่เป็นนักเรียนทุน
                                                   </label>
                                               </div>
                                               <div class="radio">
                                                   <label>
                                                       <input type="radio" name="optionsRadios" id="optionsRadios2" value="option2">นักเรียนทุน
                                                   </label>
                                               </div>

                                                <br>


                                                <div class="form-inline">
                                             <div class="form-group">
                                                 <label>สังกัด <font color ="red">*</font> </label>
                                                 <select style="width:300px" class="form-control" name="position">
                                                   <?php foreach ($faculty as $faculty): ?>
                                                     <option value="<?=$faculty['FacID']?>"><?=$faculty['FacNameT']?></option>
                                                   <?php endforeach; ?>
                                                 </select>
                                             </div>
                                              &nbsp;
                                             <div class="form-group">
                                                 <label>ภาควิชา <font color ="red">*</font> </label>
                                                 <select style="width:200px" class="form-control" name="position">
                                                   <?php foreach ($department as $department): ?>
                                                     <option value="<?=$department['DeptID']?>"><?=$department['DeptNameT']?></option>
                                                   <?php endforeach; ?>
                                                 </select>
                                             </div>
                                            </div>

                                            <div class="row" style="margin-top: 2rem">
                                              <div class="form-group col-sm-3">
                                                  <label>ตำแหน่ง <font color ="red">*</font> </label>
                                                  <select class="form-control" name="position">
                                                    <?php foreach ($positions as $position): ?>
                                                      <option value="<?=$position['PositionID']?>"><?=$position['PositionName']?></option>
                                                    <?php endforeach; ?>
                                                  </select>
                                              </div>
                                            </div>



                                              <div class="form-group">
                                            <label>ประเภทบุคลากร <font color ="red">*</font> </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="status1" value="1" checked> ข้าราชการ
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="status1" value="2">พนักงานมหาวิทยาลัย
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="status1" value="3">ลูกจ้างประจำ
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="status1" value="4">กรณีคณะ/สำนัก/หน่วยงาน
                                            </label>
                                              </div>
                                              <br>

                                            <div class="form-inline">
                                              <div class="form-group">
                                            <label>สถานภาพ <font color ="red">*</font> </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="mstatus" value="1" checked> โสด
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="mstatus" value="2">สมรส
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="mstatus" value="4">สมรสบุคลากรในมหาวิทยาลัยอุบลฯ
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="mstatus" value="3">หย่า
                                            </label>
                                              </div>

                                              <div class="form-group">
                                            <label> <b>|</b> จำนวนบุตร <font color ="red">*</font> </label>
                                            <input style="width:50px" class="form-control" maxlength = "2"name="son"> <lebel>คน</lebel>
                                              </div>
                                            </div>

                                              <br>
                                              <div class="row">
                                                <div class="form-group col-sm-5" style="margin-top: 1rem">
                                                    <label>ที่อยู่ตามทะเบียนบ้าน <font color ="red">*</font></label>
                                                    <textarea class="form-control" rows="3" name="address1"></textarea>
                                                </div>
                                              </div>

                                            <div class="row">
                                              <div class="form-grou col-sm-4">
                                                  <label>จังหวัด <font color ="red">*</font></label>
                                                  <select class="form-control" name="province1" onchange="fetchcity(this.value);">
                                                    <?php foreach ($provinces as $province): ?>
                                                      <option value="<?=$province['ProvinceID']?>"><?=$province['ProvinceNameT']?></option>
                                                    <?php endforeach; ?>
                                                  </select>
                                              </div>
                                            </div>

                                            <div class="row">
                                              <div class="form-group col-sm-4">
                                                  <label>อำเภอ <font color ="red">*</font></label>
                                                  <select class="form-control" name="city1">
                                                    <option value="" id="defoption">---โปรดเลือกจังหวัด---</option>
                                                  </select>
                                              </div>
                                            </div>

                                            <div class="row">
                                              <div class="form-group col-sm-5">
                                            <label>ที่พักอาศัยปัจจุบัน <font color ="red">*</font> </label>
                                            <textarea class="form-control" rows="3" name="addressCur"></textarea>
                                              </div>
                                            </div>


                                            <label>บรรจุเข้าปฎิบัติงานในมหาวิทยาลัยอุบลราชธานีเมื่อ </label>
                                            <div class="form-inline" style="margin-top: 2rem">
                                              <div class="form-group">

                                                  <label> วันที่ <font color ="red">*</font></label>

                                                  <select class="form-control" name="day">
                                                    <?PHP for($i=1; $i<=31; $i++)
                                                    {?>
                                                      <option value="<?PHP echo str_pad($i, 2, '0', STR_PAD_LEFT);?>"><?PHP echo $i?></option>
                                              <?PHP } ?>
                                              </select>
                                            </div>

                                                  <label>เดือน <font color ="red">*</font></label>
                                                  <select  class="form-control" name="month">
                                                    <?PHP $month = array("มกราคม ", "กุมภาพันธ์ ", "มีนาคม ", "เมษายน ", "พฤษภาคม ", "มิถุนายน ", "กรกฎาคม ", "สิงหาคม ", "กันยายน ", "ตุลาคม ", "พฤศจิกายน ", "ธันวาคม "); ?>
                                                    <?PHP for($k=0; $k<sizeof($month); $k++) {?>
                                                      <option value="<?PHP echo str_pad($k+1, 2, '0', STR_PAD_LEFT);?>" > <?PHP echo $month[$k]?></option>
                                                        <?PHP } ?>
                                                      </select>

                                                  <label>ปี พ.ศ. <font color ="red">*</font></label>
                                                  <select class="form-control" name="year">
                                                    <?PHP for($j=2500; $j<=2600; $j++)
                                                    {?>
                                                      <option value="<?PHP echo $j - 543?>"><?PHP echo $j?></option>
                                              <?PHP } ?>
                                              </select>
                                              </div>
                                            </div>

                                            <div class="form-inline">
                                              <div class="form-group">
                                            <label>เบอร์โทรศัพท์ <font color ="red">*</font></label>
                                            <input style="width:150px" class="form-control" maxlength = "15" name="telephone">
                                              </div>
                                              &nbsp;
                                            <label>Email</label>
                                            <input style="width:200px" class="form-control" maxlength = "30" name="email">
                                              </div>

                                              <br>
                                              <hr>

                                            <div class="form-group">

                                              <label><font color ="blue">ส่วนที่ 2 : เลือกประเภทห้องพัก</font></label>
                                                <h5><font color = "red">(แฟลต 6 จัดสรรให้เฉพาะบุคลากร ว.แพทย์ คณะเภสัชศาสตร์และพยาบาลศาสตร์)*</font></h5>

                                                  <div class="form-inline">
                                                  <div class="form-group">
                                                      <select  style="width:200px"  class="form-control" name="roomtype1" onchange="changevalueflat(this.value);">
                                                        <option value="">--- เลือกประเภทห้อง ---</option>
                                                        <option value="1">โสด</option>
                                                        <option value="2">ครอบครัว</option>
                                                      </select>
                                                  </div>

                                                  <div class="form-group"> <font color ="red"> &nbsp; <lebel>*</lebel></font>
                                                      <select  style="width:150px" class="form-control" name="building1">
                                                        <option value="">--- เลือกแฟลต --- </option>
                                                        <option value="1">1</option>
                                                        <option value="2">2</option>
                                                        <option value="3">3</option>
                                                        <option value="4">4</option>
                                                        <option value="5">5</option>
                                                        <option value="5">6</option>
                                                      </select>
                                                  </div> &nbsp; <font color ="red"><lebel>*</lebel></font>
                                                </div>


                                                  </div>

                                            <button type="submit" class="btn btn-success">ตกลง</button>
                                            <button type="reset" class="btn btn-danger">Reset</button>
                                            <br></br>
                                          </form>
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

    <!-- Morris Charts JavaScript -->
    <script src="js/plugins/morris/raphael.min.js"></script>
    <script src="js/plugins/morris/morris.min.js"></script>
    <script src="js/plugins/morris/morris-data.js"></script>
    <script>
    $('input[name="optionsRadios"]').on('change', function () {
      console.log(this.value);
      if (this.value == 'option2') {
        $('input[name="inputIntern"]').show();
      } else {
        $('input[name="inputIntern"]').hide();
      }
    });

    $('input[name="optionsRadiosInline"]').on('change', function () {
      console.log(this.value);
      if (this.value == 'option4') {
        $('input[name="inputOrg"]').show();
      } else {
        $('input[name="inputOrg"]').hide();
      }
    });


    function fetchcity(value) {
      var url = "getcity.php?id=" + value;

      $.get(url, function(response) {
        $('select[name="city1"] option').remove();

        $('select[name="city1"]').html(response);
      });
    }

    function changevalueflat(value) {
      if (value == '1') {
        $('select[name="building1"] option[value="5"]').hide();
        $('select[name="building1"] option[value="6"]').show();

        $('select[name="building1"] option[value="1"]').show();

      }

      if (value == '2') {
        $('select[name="building1"] option[value="5"]').show();
        $('select[name="building1"] option[value="6"]').show();

        $('select[name="building1"] option[value="1"]').hide();
      }
    }
    </script>

</body>

</html>
