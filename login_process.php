<?php
session_start();
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            // Login สำเร็จ
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            header("Location: index.php");
            exit;
        } else {
            // รหัสผ่านผิด
            $_SESSION['error_message'] = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง!";
            header("Location: login.php"); // <-- เพิ่มเข้ามา
            exit; // <-- เพิ่มเข้ามา
        }
    } else {
        // ไม่พบชื่อผู้ใช้
        $_SESSION['error_message'] = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง!";
        header("Location: login.php"); // <-- เพิ่มเข้ามา
        exit; // <-- เพิ่มเข้ามา
    }

    $stmt->close();
    $conn->close();
    // ไม่จำเป็นต้องมี header("Location: index.php"); ตรงนี้แล้ว
}
?>