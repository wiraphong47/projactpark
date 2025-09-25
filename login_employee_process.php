<?php
session_start();
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // ตรวจสอบรหัสผ่าน และต้องมี role เป็น 'employee' เท่านั้น
        if (password_verify($password, $user['password']) && $user['role'] === 'employee') {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = 'employee';

            // บันทึกการล็อกอินลงในตาราง login_history
            $stmt_login = $conn->prepare("INSERT INTO login_history (user_id, login_time) VALUES (?, NOW())");
            $stmt_login->bind_param("i", $user['id']);
            $stmt_login->execute();
            
            // บันทึก ID ของรายการที่บันทึกไว้ใน session
            $_SESSION['login_history_id'] = $conn->insert_id;
            $stmt_login->close();

            // เปลี่ยนการ redirect ให้ไปที่หน้า Dashboard
            header("Location: admin_dashboard.php"); 
            exit;
        }
    }
    // ถ้าผิดพลาด ให้กลับไปหน้า employee login พร้อม error
    header("Location: login_employee.php?error=ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง หรือคุณไม่ใช่พนักงาน");
    exit;
}
?>