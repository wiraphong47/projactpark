<?php
session_start();
require_once 'config.php';

// ตรวจสอบว่าเป็น Admin หรือไม่ และมีข้อมูลส่งมาถูกต้อง
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin' || !isset($_POST['spot_name'])) {
    header('Location: index.php');
    exit();
}

$spot_to_reset = $_POST['spot_name'];

// อัปเดตสถานะกลับเป็น 'available' และล้างข้อมูลผู้จอง
$stmt = $conn->prepare("UPDATE parking_spots SET status = 'available', booked_by_user = NULL WHERE spot_name = ?");$stmt->bind_param("s", $spot_to_reset);
$stmt->execute();
$stmt->close();
$conn->close();

// กลับไปหน้า dashboard ของ admin
header('Location: admin_dashboard.php');
exit();
?>