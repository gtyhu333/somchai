<?php

require 'DBconnect.php';

$stmt = $db->prepare("SELECT * FROM member WHERE UserID = ? LIMIT 1");
$stmt->bindParam(1, $_GET['id'], PDO::PARAM_INT);
$stmt->execute();
$stmt->setFetchMode(PDO::FETCH_ASSOC);
$user = $stmt->fetch();

try {
  $stmt = $db->prepare("SELECT * FROM allvars WHERE FieldName = 'UserType'");
  $stmt->execute();
  $stmt->setFetchMode(PDO::FETCH_ASSOC);
  $userTypes = $stmt->fetchAll();
} catch(PDOException $e) {
  echo "Error: " . $e->getMessage();
}

?>
<form action="update_user.php" method="post">
    <div class="form-group">
        <label for="username">Username</label>
        <input type="text" class="form-control" name="username" placeholder="Username" 
        value="<?= $user['UserLogin'] ?>" required>
    </div>

    <div class="form-group">
        <label for="username">Password</label>
        <input type="password" class="form-control" name="password" placeholder="Password">
        <span class="help-block">เว้นว่างหากไม่ต้องการแก้ไข</span>
    </div>

    <div class="form-group">
        <label for="usertype">ประเภทสมาชิก</label>
        <select name="usertype" class="form-control" required>
            <option value="">โปรดเลือก</option>
            <?php foreach ($userTypes as $type): ?>
            <option value="<?= $type['FieldCode'] ?>"<?= $user['UserType'] == $type['FieldCode'] ? ' selected' : '' ?>>
                <?= $type['ValueT'] ?>
            </option>
            <?php endforeach ?>
        </select>
    </div>

    <input type="hidden" name="userid" value="<?= $user['UserID'] ?>">
    <button type="submit" class="btn btn-primary">แก้ไข</button>
</form>
