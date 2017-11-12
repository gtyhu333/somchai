<?php

require 'DBconnect.php';

$stmt = $db->prepare("SELECT * FROM repairform_pic WHERE form_id = ? ");
$stmt->bindParam(1, $_GET['id'], PDO::PARAM_INT);
$stmt->execute();
$stmt->setFetchMode(PDO::FETCH_ASSOC);
$result = $stmt->fetchAll();

?>

<?php foreach ($result as $pic): ?>
  <img src="<?= $pic['path'] ?>" height="300" style="display: block; margin: 0 auto 10px;">
<?php endforeach; ?>
