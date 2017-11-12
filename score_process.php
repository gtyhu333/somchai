<?php

include 'DBConnect.php';

function process($request)
{
    global $db;
    $score = 0;

    $score += $postionScore = postionScore($request['PositionID'], $db);
    $score += $cityScore = cityScore($request['CityID'], $db);
    $score += $disasterScore = $request['DisasterPic'] ? 5 : 0;

    switch ($request['MaritalStatus']) {
        case '1':
        case '3':
            $score += $maritalScore = 1;
            break;
        case '2':
            $score += $maritalScore = 2;
            break;
        case '4':
            $score += $maritalScore = 3;
            break;
    }

    $score += $request['ChildrenCount'];
    $maritalScore += $request['ChildrenCount'];

    $interval = date_diff(new DateTime($request['EmployDate']), new DateTime("now"));

    if ($interval->m > 6) {
        $score += $employScore = $interval->y + 1;
    } elseif ($interval->m < 6) {
        $score += $employScore = ($interval->m / 12) + $interval->y;
    }

    return [
        'StaffID' => $request['StaffID'], 
        'Score' => round($score, 2),
        'Scores' => [
            'postionScore' => $postionScore,
            'cityScore' => $cityScore,
            'disasterScore' => $disasterScore,
            'maritalScore' => $maritalScore,
            'employScore' => $employScore,
        ]
    ];
}

$range = [
    '1' => [date('Y') . '-01-01', date('Y') . '-04-01'],
    '2' => [date('Y') . '-04-02', date('Y') . '-08-01'],
    '3' => [date('Y') . '-08-02', date('Y') . '-12-31'],
];

$selector = $_POST['range'];

$selectorPrevious = $selector != 1 ? $selector - 1 : 3;

$sql = "SELECT * FROM `staff` 
WHERE `staff`.`RequestDate` BETWEEN '{$range[$selector][0]}' AND '{$range[$selector][1]}' 
AND `staff`.`request_status` = 'ปกติ' 
AND NOT EXISTS 
(SELECT * FROM `score` WHERE `score`.`StaffID` = `staff`.`StaffID`);";

$stmt = $db->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($result)) {
    goto updateCycle;
}

$insertSQL = "
    INSERT INTO `score` (`StaffID`, `PositionScore`, 
    `CityScore`, `DisasterScore`, `MaritalScore`, `EmployScore`,`Score`) VALUES ";

foreach ($scores = array_map("process", $result) as $index => $score) {
    if ($score !== end($scores)) {
        $insertSQL .= 
        "({$score['StaffID']}, 
        {$score['Scores']['postionScore']}, 
        {$score['Scores']['cityScore']}, 
        {$score['Scores']['disasterScore']}, 
        {$score['Scores']['maritalScore']}, 
        {$score['Scores']['employScore']}, 
        {$score['Score']}), ";
    } else {
        $insertSQL .= 
        "({$score['StaffID']}, 
        {$score['Scores']['postionScore']}, 
        {$score['Scores']['cityScore']}, 
        {$score['Scores']['disasterScore']}, 
        {$score['Scores']['maritalScore']}, 
        {$score['Scores']['employScore']}, 
        {$score['Score']});";
    }
}

$stmt = $db->prepare($insertSQL);
$stmt->execute();

// Update the current cycle
updateCycle:

$sql = "SELECT * FROM `staff` 
WHERE `staff`.`RequestDate` BETWEEN '{$range[$selector][0]}' AND '{$range[$selector][1]}' 
AND `staff`.`request_status` = 'ปกติ';";

$stmt = $db->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($scores = array_map("process", $result) as $index => $score) {
    $updateSQL = "
    UPDATE `score` SET 
    `Score` = {$score['Score']}, 
    `PositionScore` = {$score['Scores']['postionScore']}, 
    `CityScore` = {$score['Scores']['cityScore']}, 
    `DisasterScore` = {$score['Scores']['disasterScore']}, 
    `MaritalScore` = {$score['Scores']['maritalScore']}, 
    `EmployScore` = {$score['Scores']['employScore']}, 
    `EvaluateDate` = now() 
    WHERE `StaffID` = {$score['StaffID']};";
    // dd($updateSQL);
    $stmt = $db->prepare($updateSQL);
    $stmt->execute();
}

// Previous Cycle
$sql = "SELECT * FROM `staff` 
WHERE `staff`.`RequestDate` BETWEEN '{$range[$selectorPrevious][0]}' AND '{$range[$selectorPrevious][1]}' 
AND `staff`.`request_status` = 'ปกติ';";

$stmt = $db->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($scores = array_map("process", $result) as $index => $score) {
    $updateSQL = "
    UPDATE `score` SET 
    `Score` = {$score['Score']}, 
    `PositionScore` = {$score['Scores']['postionScore']}, 
    `CityScore` = {$score['Scores']['cityScore']}, 
    `DisasterScore` = {$score['Scores']['disasterScore']}, 
    `MaritalScore` = {$score['Scores']['maritalScore']}, 
    `EmployScore` = {$score['Scores']['employScore']}, 
    `EvaluateDate` = now() 
    WHERE `StaffID` = {$score['StaffID']};";
    $stmt = $db->prepare($updateSQL);
    $stmt->execute();
}

header("Location: form_process.php");
exit();

