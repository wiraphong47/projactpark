<?php
session_start();
require_once 'config.php';

// ตรวจสอบว่าเป็น Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    exit('Access Denied');
}

// --- Logic การเพิ่มโซน ---
if (isset($_POST['add_zone'])) {
    $zone_name = $_POST['zone_name'];
    if (!empty($zone_name)) {
        $stmt = $conn->prepare("INSERT INTO zones (name) VALUES (?)");
        $stmt->bind_param("s", $zone_name);
        $stmt->execute();
        $stmt->close();
    }
}

// --- Logic การลบโซน ---
if (isset($_GET['delete_zone'])) {
    $zone_id = $_GET['delete_zone'];
    if (filter_var($zone_id, FILTER_VALIDATE_INT)) {
        // (สำคัญ) ควรตรวจสอบก่อนว่ามีที่จอดในโซนนี้หรือไม่ก่อนลบ
        $stmt = $conn->prepare("DELETE FROM zones WHERE id = ?");
        $stmt->bind_param("i", $zone_id);
        $stmt->execute();
        $stmt->close();
    }
}

$conn->close();
// กลับไปยังหน้าจัดการโซน
header("Location: manage_zones.php");
exit;
?>