<?php
session_start();
require_once 'config.php';

// ตรวจสอบว่าล็อกอินแล้ว และมีข้อมูลที่จอดส่งมาหรือไม่
if (!isset($_SESSION['username']) || !isset($_POST['booked_spot'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];
$booked_spot = $_POST['booked_spot'];

// --- ส่วนที่สำคัญที่สุด: อัปเดตฐานข้อมูล ---
// 1. เตรียมคำสั่ง SQL เพื่ออัปเดตสถานะที่จอด
$stmt = $conn->prepare("UPDATE parking_spots SET status = 'occupied', booked_by_user = ?, booked_at = NOW() WHERE spot_name = ? AND status = 'available'");
$stmt->bind_param("ss", $username, $booked_spot);

// 2. รันคำสั่ง
$stmt->execute();

// 3. ตรวจสอบว่าการอัปเดตสำเร็จหรือไม่ (เช็คว่ามีแถวที่ได้รับผลกระทบหรือไม่)
$is_success = $stmt->affected_rows > 0;

$stmt->close();
$conn->close();

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>การจองสำเร็จ</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .success-container { text-align: center; background: #fff; padding: 40px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .success-container h1 { color: #28a745; }
        .back-button { display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="success-container">
        <?php if ($is_success): ?>
            <h1>🎉 การจองสำเร็จ!</h1>
            <p>คุณได้ทำการจองที่จอด <strong><?php echo htmlspecialchars($booked_spot); ?></strong> เรียบร้อยแล้ว</p>
        <?php else: ?>
            <h1 style="color: #dc3545;">❗️เกิดข้อผิดพลาด</h1>
            <p>ที่จอด <strong><?php echo htmlspecialchars($booked_spot); ?></strong> อาจถูกจองไปก่อนหน้านี้แล้ว</p>
        <?php endif; ?>
        
        <a href="index.php" class="back-button">กลับสู่หน้าหลัก</a>
    </div>
</body>
</html>