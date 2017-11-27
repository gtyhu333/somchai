<?php
require 'DBConnect.php';

function getExpenseType()
{
    global $db;

    $stmt = $db->prepare("SELECT * FROM expensetype");
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    return $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Test PDF Form</title>
</head>
<body>
    <form action="testpdfprint.php" method="POST">
        <h2>รายการ</h2>
        <table border="1" style="margin: 20px 0; width: 100%; max-width: 500px;">
            <thead>
                <tr>
                    <th>ชื่อ</th>
                    <th>จำนวน (บาท)</th>
                    <th>เพิ่มลบ</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <input type="text" list="list1" name="name[]" style="width: 90%"
                        oninput="getPrice(this)">
                    </td>
                    <td class="pricetr">
                        <input type="text" value="" name="price[]" style="width: 90%">
                    </td>
                    <td>
                        <button type="button" 
                        onclick="clone(this.parentNode.parentNode)">+</button>

                        <button type="button" 
                        onclick="del(this.parentNode.parentNode)" class="delbtn"
                        disabled="true">-</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <datalist id="list1">
            <?php foreach (getExpenseType() as $type): ?>
            <option value="<?= $type['ExpenseName'] ?>">
                <?= $type['ExpenseName'] ?>
            </option>
            <?php endforeach ?>
        </datalist>

        <button type="submit">Create</button>
    </form>
</body>
<script src="js/jquery.js"></script>
<script>
var rowCount = 1;

function getPrice(element) {
    let value = element.value;
    let input = element.parentNode.parentNode.querySelector(".pricetr > input");

    $.get("get_expense_price.php", { name: value }, function (response) {
        let result = JSON.parse(response).DefaultRate;

        if (result != 0) {
            input.value = JSON.parse(response).DefaultRate;
        }
    });
}

function clone(row) {
    row.parentNode.appendChild(row.cloneNode(true));

    if (++rowCount > 1) {
        document.querySelectorAll("button.delbtn").forEach(function(input) {
            input.disabled = false;
        });
    }
}

function del(row) {
    row.parentNode.removeChild(row);

    if (--rowCount == 1) {
        document.querySelectorAll("button.delbtn").forEach(function(input) {
            input.disabled = true;
        });
    }
}
</script>
</html>
