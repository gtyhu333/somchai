<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>
    <form role="form" action="test_reg.php" method="POST">
    <div class="form-group">
    <label>ชื่อผู้ใช้</label>
    <input type="text" class="form-control" name="Username" required>
    </div>

    <div class="form-group">
    <label>รหัสผ่าน</label>
    <input type="pass" class="form-control" name="Pass" required>
    </div>

    <div class="form-group">
    <label>กรอก Type</label>
    <input type="pass" class="form-control" name="Usertype" required>
    </div>


    <div class="form-group">
    <label>กรอก เลขตำแหน่ง</label>
    <input type="pass" class="form-control" name="Position" required>
    </div>
    <button type="submit" class="btn btn-default">Submit Button</button>
    </form>
  </body>
</html>
