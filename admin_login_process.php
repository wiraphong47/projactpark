<?php
session_start();
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // ตรวจสอบรหัสผ่าน และต้องมี role เป็น 'admin' เท่านั้น
        if (password_verify($password, $user['password']) && $user['role'] === 'admin') {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = 'admin';
            header("Location: admin_dashboard.php");
            exit;
        }
    }
    // ถ้าผิดพลาด ให้กลับไปหน้า admin login พร้อม error
    header("Location: admin_login.php?error=ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง หรือคุณไม่ใช่ผู้ดูแลระบบ");
    exit;
}
?>