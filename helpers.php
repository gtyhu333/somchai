<?php

function positionName($postionID, $db)
{
    $stmt = $db->prepare("SELECT PositionName FROM position WHERE PositionID = ? LIMIT 1;");
    $stmt->bindParam(1, $postionID, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['PositionName'];
}

function facName($facID, $db)
{
    $stmt = $db->prepare("SELECT FacNameT FROM faculty WHERE FacID = ? LIMIT 1;");
    $stmt->bindParam(1, $facID, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['FacNameT'];
}

function sqlDateToThaiDate($date)
{
    $months = [
        '', "ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค."
    ];

    if (! $date) {
        return;
    }

    $explode = explode('/', date('j/n/Y', strtotime($date)));

    $explode[1] = $months[$explode[1]];

    $explode[2] = ((int) $explode[2]) + 543;

    return implode(' ', $explode);
}

function getFullAddress($request, $db)
{
    return ' อ.' .
           cityName($request['CityID'], $db) . ($request['ProvinceID'] == '1' ? ' ' : ' จ.') .
           provinceName($request['ProvinceID'], $db);
}

function districtName($districtID, $db)
{
    $stmt = $db->prepare("SELECT DistrictNameT FROM district WHERE DistrictID = ? LIMIT 1;");
    $stmt->bindParam(1, $districtID, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['DistrictNameT'];
}

function cityName($cityID, $db)
{
    $stmt = $db->prepare("SELECT CityNameT FROM city WHERE CityID = ? LIMIT 1;");
    $stmt->bindParam(1, $cityID, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['CityNameT'];
}

function provinceName($provinceID, $db)
{
    $stmt = $db->prepare("SELECT ProvinceNameT FROM province WHERE ProvinceID = ? LIMIT 1;");
    $stmt->bindParam(1, $provinceID, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['ProvinceNameT'];
}

function postionScore($postionID, $db)
{
    $stmt = $db->prepare("SELECT Weight FROM position WHERE PositionID = ? LIMIT 1;");
    $stmt->bindParam(1, $postionID, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['Weight'];
}

function cityScore($cityID, $db)
{
    $stmt = $db->prepare("SELECT Weight FROM city WHERE CityID = ? LIMIT 1;");
    $stmt->bindParam(1, $cityID, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['Weight'];
}

function roomNumber($roomID, $db)
{
    $stmt = $db->prepare("SELECT RoomName FROM room WHERE RoomID = ? LIMIT 1;");
    $stmt->bindParam(1, $roomID, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['RoomName'];
}

function memberType($userID, $db)
{
    $resultMember = getUserInfo($userID, $db);

    $stmt = $db->prepare("SELECT ValueT FROM allvars WHERE FieldName = 'UserType'
    AND FieldCode = ? LIMIT 1;");
    $stmt->bindParam(1, $resultMember['UserType'], PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return [$resultMember['UserType'] , $result['ValueT']];
}

function getDepartments($facID, $db)
{
    $stmt = $db->prepare("SELECT * FROM department WHERE FacID = ?;");
    $stmt->bindParam(1, $facID, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getUserInfo($userID, $db)
{
    $stmt = $db->prepare("SELECT * FROM member WHERE UserID = ? LIMIT 1;");
    $stmt->bindParam(1, $userID, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getScore($staffID, $db)
{
    $stmt = $db->prepare("SELECT * FROM score WHERE StaffID = ? ORDER BY EvaluateDate DESC LIMIT 1;");
    $stmt->bindParam(1, $staffID, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result;
}

function getBuildingTableClass($status)
{
    switch ($status) {
        case 'ว่าง ไม่พร้อมใช้งาน':
            return 'warning';
            break;
        case 'มีผู้พักอาศัย':
            return 'info';
            break;
    }
}

function getBuidlingName($id, $db)
{
    $stmt = $db->prepare("SELECT * FROM building WHERE BuildingID = ? LIMIT 1;");
    $stmt->bindParam(1, $id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['BuildingName'];
}

function getRoomTypeName($id, $db)
{
    $stmt = $db->prepare("SELECT * FROM allvars WHERE FieldName = 'RoomType' AND FieldCode = ? LIMIT 1;");
    $stmt->bindParam(1, $id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['ValueT'];
}

function getAvailableRoomForRequest($request, $db)
{
    $stmt = $db->prepare("
        SELECT * FROM room WHERE NOT EXISTS (SELECT RoomID FROM resident WHERE `resident`.`RoomID` = `room`.`RoomID`)
        AND RoomType = ?;
    ");

    $stmt->bindParam(1, $request['RoomtypeID'], PDO::PARAM_INT);

    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $groupedResult = [];

    foreach ($result as $key => $room) {
        $groupedResult[$room['BuildingID']][$key] = $room;
    }

    // dd($groupedResult);

    return $groupedResult;
}

function thaiDateToSqlFormat($dateString)
{
    $dateArray = explode('/', $dateString);

    if (strlen($dateArray[2]) == 2) {
        $dateArray[2] += 2500;
    }

    $dateArray[2] -= 543;

    return date_create_from_format(
        'd/m/Y', implode('/', $dateArray)
    )->format('Y-m-d');
}

function getRoomID($roomName, $buildingID, $db)
{
    $stmt = $db->prepare("SELECT RoomID FROM room
        WHERE RoomName = :roomname AND BuildingID = :buildingid LIMIT 1;");

    $stmt->bindParam(':roomname', $roomName);
    $stmt->bindParam(':buildingid', $buildingID);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['RoomID'];
}

function getRoomBuildingName($id, $db)
{
  $stmt = $db->prepare("SELECT * FROM v_room WHERE RoomID = ? LIMIT 1;");
  $stmt->bindParam(1, $id, PDO::PARAM_INT);
  $stmt->execute();
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  return $result['BuildingName'];
}

function getResidentInfo($userID, $db)
{
    $stmt = $db->prepare("SELECT * FROM v_resident WHERE ResidentID = ? LIMIT 1;");
    $stmt->bindParam(1, $userID, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function dd($var)
{
    echo "<pre>";
    var_dump($var);
    echo "</pre>";

    die();
}

function array_take($array, $keys)
{
    $returnArray = [];

    foreach ($keys as $key) {
        $returnArray[$key] = $array[$key];
    }

    return $returnArray;
}

function map($array, callable $closure)
{
    $result = [];

    foreach ($array as $key => $value) {
        $result[] = $closure($value, $key);
    }

    return $result;
}

function getUserFullName($id, $db)
{
    $userData = getUserInfo($id, $db);

    return "{$userData['UserPNameT']}${userData['UserNameT']} {$userData['UserSNameT']}";
}

function Convert($amount_number)
{
    $amount_number = number_format($amount_number, 2, ".","");
    $pt = strpos($amount_number , ".");
    $number = $fraction = "";
    if ($pt === false) 
        $number = $amount_number;
    else
    {
        $number = substr($amount_number, 0, $pt);
        $fraction = substr($amount_number, $pt + 1);
    }
    
    $ret = "";
    $baht = ReadNumber ($number);
    if ($baht != "")
        $ret .= $baht . "บาท";
    
    $satang = ReadNumber($fraction);
    if ($satang != "")
        $ret .=  $satang . "สตางค์";
    else 
        $ret .= "ถ้วน";
    return $ret;
}

function ReadNumber($number)
{
    $position_call = array("แสน", "หมื่น", "พัน", "ร้อย", "สิบ", "");
    $number_call = array("", "หนึ่ง", "สอง", "สาม", "สี่", "ห้า", "หก", "เจ็ด", "แปด", "เก้า");
    $number = $number + 0;
    $ret = "";
    if ($number == 0) return $ret;
    if ($number > 1000000)
    {
        $ret .= ReadNumber(intval($number / 1000000)) . "ล้าน";
        $number = intval(fmod($number, 1000000));
    }
    
    $divider = 100000;
    $pos = 0;
    while($number > 0)
    {
        $d = intval($number / $divider);
        $ret .= (($divider == 10) && ($d == 2)) ? "ยี่" : 
            ((($divider == 10) && ($d == 1)) ? "" :
            ((($divider == 1) && ($d == 1) && ($ret != "")) ? "เอ็ด" : $number_call[$d]));
        $ret .= ($d ? $position_call[$pos] : "");
        $number = $number % $divider;
        $divider = $divider / 10;
        $pos++;
    }
    return $ret;
}
