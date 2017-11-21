<?php

require 'DBconnect.php';

$stmt = $db->prepare("SELECT * FROM district WHERE CityID = ? ");
$stmt->bindParam(1, $_GET['id'], PDO::PARAM_INT);
$stmt->execute();
$stmt->setFetchMode(PDO::FETCH_ASSOC);
$result = $stmt->fetchAll();

?>

<?php foreach ($result as $district): ?>
  <option value="<?=$district['DistrictID']?>"><?=$district['DistrictNameT']?></option>
<?php endforeach; ?>
