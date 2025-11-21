<?php
$host="localhost";
$dbname="rpsu_lostfound";
$user="root";
$pass="";

try{
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4",$user,$pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){
    die("Connection failed: ".$e->getMessage());
}
?>
