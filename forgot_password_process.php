<?php
session_start();
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username_email = $_POST['username_email'];

    // ตรวจสอบว่าผู้ใช้มีอยู่ในฐานข้อมูลหรือไม่
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $user_id = $row['id'];
        
        // --- ขั้นตอนต่อไป: สร้างและบันทึกโทเค็นสำหรับรีเซ็ต
        // (*** จำลองการสร้างโทเค็นเท่านั้น ไม่ได้ส่งอีเมลจริง ***)
        $token = bin2hex(random_bytes(32)); 
        
        // *** หมายเหตุ: ควรสร้างตาราง reset_tokens ในฐานข้อมูลเพื่อเก็บโทเค็นนี้ ***
        // เช่น ตาราง reset_tokens มีคอลัมน์ user_id, token, expires_at
        
        // โค้ดสำหรับส่งอีเมล (เป็นเพียงตัวอย่าง)
        $reset_link = "http://yourwebsite.com/reset_password.php?token=" . $token;
        // mail($username_email, "Password Reset", "Click here to reset your password: " . $reset_link);

        // แสดงข้อความว่าส่งลิงก์ไปแล้ว
        $_SESSION['reset_success'] = "ลิงก์สำหรับรีเซ็ตรหัสผ่านถูกส่งไปยังชื่อผู้ใช้/อีเมลของคุณแล้ว";
    } else {
        // แจ้งข้อความทั่วไปเพื่อความปลอดภัย ไม่ระบุว่าชื่อผู้ใช้ไม่มีจริง
        $_SESSION['reset_error'] = "หากชื่อผู้ใช้/อีเมลนี้มีอยู่ในระบบ ลิงก์สำหรับรีเซ็ตรหัสผ่านจะถูกส่งไปให้";
    }

    $stmt->close();
    $conn->close();
    header("Location: forgot_password.php");
    exit;
}
?>