<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	<title>Access Control System</title>
	<meta name="generator" content="Bootply" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<!--[if lt IE 9]>
	<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<link href="css/styles.css" rel="stylesheet">
</head>
<body style="background-color:#222">
	<!--login modal-->
	<div id="loginModal" class="modal show" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h1 class="text-center">เข้าสู่ระบบ</h1><hr>
					<center><h3>หอพักบุคลากร มหาวิทยาลัยอุบลราชธานี</h3></center>
				</div>
				<div class="modal-body">
					<?php if (isset($_SESSION['Error'])): ?>
						<div class="alert alert-danger alert-dismissable">
							<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
							<strong>เกิดข้อผิดพลาด!</strong> <?= $_SESSION['Error'] ?>
						</div>
						<?php unset($_SESSION['Error']); ?>
					<?php endif; ?>
					<form class="form col-md-12 center-block" method="post" action="checklogin.php">
						<div class="form-group">
							<input type="text" class="form-control input-lg" placeholder="Username" name="user">
						</div>
						<div class="form-group">
							<input type="password" class="form-control input-lg" placeholder="Password" name="pass">
						</div>
						<div class="form-group">
							<button type="submit" class="btn btn-primary btn-lg btn-block">ลงชื่อเข้าใช้</button><br>

						</div>
					</form>
				</div>
				<div class="modal-footer">
					<div class="col-md-12">
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- script references -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
</body>
</html>
