<?php
require_once 'config.php';

// --- ตั้งค่าบัญชีแอดมิน ---
$admin_user = 'admin';
$admin_pass = 'admin'; // รหัสผ่านที่เราจะใช้ล็อกอิน
$role = 'admin';

// เข้ารหัสผ่านด้วยวิธีที่ถูกต้อง
$hashed_password = password_hash($admin_pass, PASSWORD_DEFAULT);

// เตรียมคำสั่ง SQL เพื่อเพิ่มข้อมูล
$stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $admin_user, $hashed_password, $role);

// ลองเพิ่มข้อมูลและแสดงผล
if ($stmt->execute()) {
    echo "<h1>สร้างบัญชีแอดมินสำเร็จ!</h1>";
    echo "<p>ชื่อผู้ใช้: admin</p>";
    echo "<p>รหัสผ่าน: admin</p>";
    echo "<a href='admin_login.php'>ไปที่หน้าล็อกอินแอดมิน</a>";
} else {
    echo "<h1>เกิดข้อผิดพลาด!</h1>";
    echo "<p>อาจมีชื่อผู้ใช้ 'admin' อยู่ในระบบแล้ว</p>";
}

$stmt->close();
$conn->close();
?>