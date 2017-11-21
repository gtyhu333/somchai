<?php
require 'DBConnect.php';

$id = $_GET['id'];

try {
  $stmt = $db->prepare("SELECT * FROM news WHERE id = ?");
  $stmt->bindParam(1, $id);
  $stmt->execute();
  $news = $stmt->fetch(PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo "Error: " . $e->getMessage();
}
?>
<form action="news_edit.php" method="POST">
<div class="form-group">
    <label>หัวข้อข่าว</label>
    <input type="text" name="title" class="form-control" value="<?= $news['title'] ?>" required>
</div>

<div class="form-group">
    <label>เนื้อหา</label>
    <textarea name="body" cols="30" rows="10" class="form-control" required><?= $news['content'] ?></textarea>
</div>

<input type="hidden" name="id" value="<?= $id ?>">

<div class="form-group">
    <button type="submit" class="btn btn-primary">
      แก้ไข
    </button>
</div>
</form>
