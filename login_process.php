<?php
session_start();
require_once 'config.php'; // เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูล

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
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            header("Location: welcome.php");
            exit;
        } else {
            $_SESSION['error_message'] = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง!";
        }
    } else {
        $_SESSION['error_message'] = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง!";
    }

    $stmt->close();
    $conn->close();
    header("Location: index.php");
    exit;
}
?>