<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['username']) || !isset($_POST['spot_name'])) {
    header('Location: index.php');
    exit();
}

$username = $_SESSION['username'];
$spot_to_cancel = $_POST['spot_name'];

// ทำการอัปเดตสถานะกลับเป็น 'available' และล้างชื่อผู้จอง
// เงื่อนไข WHERE สำคัญมาก: ต้องมั่นใจว่าคนที่กดยกเลิกคือเจ้าของที่จองจริงๆ
$stmt = $conn->prepare("UPDATE parking_spots SET status = 'available', booked_by_user = NULL WHERE spot_name = ? AND booked_by_user = ?");
$stmt->bind_param("ss", $spot_to_cancel, $username);
$stmt->execute();

$stmt->close();
$conn->close();

// เมื่อยกเลิกสำเร็จ ให้กลับไปหน้า "การจองของฉัน"
header('Location: my_bookings.php');
exit();
?>