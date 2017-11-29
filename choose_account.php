<?php 
require 'DBconnect.php';
session_start(); 

if (!isset($_SESSION['selected_user_id'])) {
	session_destroy();
	header('Location: login.php');
	exit();
}

$user_id = $_SESSION['selected_user_id'];
unset($_SESSION['selected_user_id']);

$stmt = $db->prepare("SELECT * FROM member WHERE CopyFrom = '$user_id' OR UserID = '$user_id' ORDER BY UserType ASC");
$stmt->execute();
$altAccounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
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
					<h2 class="text-center">กรุณาเลือกสิทธิ์ในการเข้าระบบ</h2>
				</div>
				<div class="modal-body">
					<form action="checklogin_alt.php" method="POST">
						<div class="row">
							<div class="col-lg-12">
								<div class="form-group">
									<label>สิทธิ์ที่สามารถเข้าใช้งานได้</label>
									<select name="user_id" class="form-control">
									<?php foreach ($altAccounts as $account): ?>
										<option value="<?= $account['UserID'] ?>"><?= memberType($account['UserID'], $db)[1] ?></option>
									<?php endforeach ?>
									</select>
								</div>

								<div class="form-group">
									<button class="btn btn-block btn-lg btn-primary">
										เข้าสู่ระบบ
									</button>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<!-- script references -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
</body>
</html>
