<?php
session_start();
require_once 'config.php';

// บันทึกเวลาที่ล็อกเอาท์หากมี session ที่ถูกบันทึกไว้
if (isset($_SESSION['login_history_id'])) {
    $login_history_id = $_SESSION['login_history_id'];
    $stmt = $conn->prepare("UPDATE login_history SET logout_time = NOW() WHERE id = ?");
    $stmt->bind_param("i", $login_history_id);
    $stmt->execute();
    $stmt->close();
}

// ล้างข้อมูลใน session ทั้งหมด
$_SESSION = array();

// ทำลาย session ที่ใช้งานอยู่
session_destroy();

// ส่งผู้ใช้กลับไปยังหน้า login.php
header("Location: login.php");
exit;
?>