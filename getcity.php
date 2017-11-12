<?php

require 'DBconnect.php';

$stmt = $db->prepare("SELECT * FROM city WHERE ProvinceID = ? ");
$stmt->bindParam(1, $_GET['id'], PDO::PARAM_INT);
$stmt->execute();
$stmt->setFetchMode(PDO::FETCH_ASSOC);
$result = $stmt->fetchAll();

?>

<?php foreach ($result as $city): ?>
  <option value="<?=$city['CityID']?>"><?=$city['CityNameT']?></option>
<?php endforeach; ?>
