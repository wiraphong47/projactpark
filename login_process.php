<?php
session_start();
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // แก้ไข SQL ให้ดึง role มาด้วย
    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            // Login สำเร็จ
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $row['role']; // <-- บันทึก role ลง session

            // ตรวจสอบว่าเป็น admin หรือไม่
            if ($row['role'] === 'admin') {
                header("Location: admin_dashboard.php"); // ถ้าเป็น admin ให้ไปหน้า dashboard
            } else {
                header("Location: index.php"); // ถ้าเป็น user ทั่วไป ให้ไปหน้าแรก
            }
            exit;
        } else {
            // รหัสผ่านผิด
            header("Location: login.php?error=ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง");
            exit;
        }
    } else {
        // ไม่พบชื่อผู้ใช้
        header("Location: login.php?error=ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง");
        exit;
    }
}
?>