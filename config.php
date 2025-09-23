<?php
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "parking_db";

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("การเชื่อมต่อล้มเหลว: " . $conn->connect_error);
}
?>