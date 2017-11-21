<?php

require 'DBconnect.php';


$date = "{$_POST['year']}-{$_POST['month']}-{$_POST['day']}";

$stmt = $db->prepare("INSERT INTO ubu_flat.staff
  (StaffID, BirthDate, RoomID, people_id, Pname, Name, Surname,
    PositionID, PersonnelType, RepaymentRight, RightClaim, MaritalStatus,
    ChildrenCount, RequestDate, EmployDate, Address, Telephone, Email, StayStatus,
    FacID, DeptID,CityID, DistrictID, ProvinceID, oriAddress , BuildingID , RoomtypeID , S_position_ID)
    VALUES
    (NULL,NULL, NULL, :people_id, :Pname ,:Name, :Surname,
      :PositionID, :PersonnelType, '0', '0', :MaritalStatus,
      :ChildrenCount, CURRENT_TIMESTAMP, :EmployDate, :Address, :Telephone, :Email, '2',
      :FacID, :DeptID, :CityID, :DistrictID, :ProvinceID, :oriAddress, :BuildingID, :RoomtypeID , :S_position_ID);");

$stmt->execute([
  ':people_id' => $_POST['p_id'],
  ':Pname' => $_POST['pname'],
  ':Name' => $_POST['name'],
  ':Surname' => $_POST['surname'],
  ':PositionID' => $_POST['position'],
  ':PersonnelType' => $_POST['status1'],
  ':MaritalStatus' => $_POST['mstatus'],
  ':ChildrenCount' => $_POST['son'],
  ':EmployDate' => $date,
  ':Address' => $_POST['addressCur'],
  ':Telephone' => $_POST['telephone'],
  ':Email' => $_POST['email'],
  ':FacID' => $_POST['fac'],
  ':DeptID' => $_POST['dept'],
  ':CityID' => $_POST['city1'],
  ':DistrictID' => $_POST['district'],
  ':ProvinceID' => $_POST['province1'],
  ':oriAddress' => $_POST['address1'],
  ':BuildingID' => $_POST['building1'],
  ':RoomtypeID' => $_POST['roomtype1'],
  ':S_position_ID' => $_POST['status1'],
]);
    header('location:index.php');
