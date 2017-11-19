<!-- admin nav -->
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
                      <a href="building.php">จัดการอาคารที่พัก</a>
                  </li>
                  <li>
                      <div class="dropdown" style="padding: 20px;">
                        <a href="#" class="dropdown-toggle" id="financeLink" data-toggle="dropdown"
                        style="color: #777">
                          จัดการด้านการเงิน <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="financeLink">
                          <li><a href="resident_monthly_expense.php">จัดการค่าห้องประจำเดือน</a></li>
                          <li role="separator" class="divider"></li>
                          <li><a href="building_water_bill.php">จัดการค่าน้ำ</a></li>
                          <li><a href="building_electric_bill.php">จัดการค่าไฟ</a></li>
                        </ul>
                      </div>
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
