<?php

require 'DBconnect.php';


$date = "{$_POST['year']}-{$_POST['month']}-{$_POST['day']}";


// echo "$date";
//
//
// die();

$stmt = $db->prepare("INSERT INTO ubu_flat.staff
  (StaffID, BirthDate, RoomID, people_id, Name, Surname,
    PositionID, PersonnelType, RepaymentRight, RightClaim, MaritalStatus,
    ChildrenCount, RequestDate, EmployDate, Address, Telephone, Email, StayStatus,
    FacID, CityID, ProvinceID, oriAddress , BuildingID , RoomtypeID , S_position_ID)
    VALUES
    (NULL,NULL, NULL, :people_id, :Name, :Surname,
      :PositionID, :PersonnelType, '0', '0', :MaritalStatus,
      :ChildrenCount, CURRENT_TIMESTAMP, :EmployDate, :Address, :Telephone, :Email, '2',
      '1', :CityID, :ProvinceID, :oriAddress, :BuildingID, :RoomtypeID , S_position_ID);");

$stmt->execute([
  ':people_id' => $_POST['p_id'],
  ':Name' => $_POST['name'],
  ':Surname' => $_POST['surname'],
  ':PositionID' => $_POST['position'],
  ':PersonnelType' => $_POST['type'],
  ':MaritalStatus' => $_POST['mstatus'],
  ':ChildrenCount' => $_POST['son'],
  ':EmployDate' => $date,
  ':Address' => $_POST['addressCur'],
  ':Telephone' => $_POST['telephone'],
  ':Email' => $_POST['email'],
  ':CityID' => $_POST['city1'],
  ':ProvinceID' => $_POST['province1'],
  ':oriAddress' => $_POST['address1'],
  ':BuildingID' => $_POST['building1'],
  ':RoomtypeID' => $_POST['roomtype1'],
  ':S_position_ID' => $_POST['status1'],
]);
    header('location:index.php');
