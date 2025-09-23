<?php
session_start();
require_once 'config.php'; // เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูล

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['register_error'] = "ชื่อผู้ใช้ '{$username}' มีอยู่ในระบบแล้ว กรุณาเลือกชื่ออื่น!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt_insert = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt_insert->bind_param("ss", $username, $hashed_password);

        if ($stmt_insert->execute()) {
            $_SESSION['register_success'] = "การสมัครสมาชิกสำเร็จแล้ว! คุณสามารถเข้าสู่ระบบได้เลย";
        } else {
            $_SESSION['register_error'] = "เกิดข้อผิดพลาดในการสมัครสมาชิก: " . $conn->error;
        }
        $stmt_insert->close();
    }
    $stmt->close();
    $conn->close();
    header("Location: register.php");
    exit;
}
?>