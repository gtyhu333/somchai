<?php
require 'helpers.php';
error_reporting(E_ERROR | E_WARNING | E_PARSE);

$host = '127.0.0.1';
$db   = 'ubu_flat';
$user = 'root';
$pass = '';
$charset = 'utf8';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$db = new PDO($dsn, $user, $pass, $opt);

session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $db->prepare("UPDATE member SET isLogin = 1 WHERE UserID = :id");
    $stmt->bindParam(':id', $user_id);
    $stmt->execute();
}
