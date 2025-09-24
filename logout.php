<?php
// เริ่มต้น session เสมอเมื่อมีการจัดการ session
session_start();

// ล้างข้อมูลใน session ทั้งหมด
$_SESSION = array();

// ทำลาย session ที่ใช้งานอยู่
session_destroy();

// ส่งผู้ใช้กลับไปยังหน้า login.php
header("Location: login.php");
exit;
?>