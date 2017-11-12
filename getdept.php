<?php

require 'DBconnect.php';

$stmt = $db->prepare("SELECT * FROM department WHERE FacID = ? ");
$stmt->bindParam(1, $_GET['id'], PDO::PARAM_INT);
$stmt->execute();
$stmt->setFetchMode(PDO::FETCH_ASSOC);
$result = $stmt->fetchAll();

?>

<?php if (empty($result)): ?>
    <option value="0">-- ไม่มี --</option>
<?php else: ?>

<?php foreach ($result as $city): ?>
  <option value="<?=$city['DeptID']?>"><?=$city['DeptNameT']?></option>
<?php endforeach; ?>

<?php endif ?>
