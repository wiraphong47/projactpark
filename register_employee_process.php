<?php
session_start();
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $position = $_POST['position'];

    // กำหนด role ตามตำแหน่งที่เลือก
    $role = ($position === 'admin') ? 'admin' : 'employee';

    // ตรวจสอบว่าชื่อผู้ใช้มีอยู่ในระบบแล้วหรือไม่
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['register_error'] = "ชื่อผู้ใช้ '{$username}' มีอยู่ในระบบแล้ว กรุณาเลือกชื่ออื่น!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // ขั้นตอนที่ 1: บันทึกข้อมูลเบื้องต้น
        $stmt_insert = $conn->prepare("INSERT INTO users (username, password, full_name, phone_number, address, role, position) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt_insert->bind_param("sssssss", $username, $hashed_password, $full_name, $phone_number, $address, $role, $position);

        if ($stmt_insert->execute()) {
            
            // ขั้นตอนที่ 2: ดึง id ที่สร้างขึ้นอัตโนมัติ
            $new_user_id = $conn->insert_id;

            // ขั้นตอนที่ 3: สร้างรหัสพนักงานในรูปแบบ EMP-XXX
            $employee_id = 'EMP-' . str_pad($new_user_id, 3, '0', STR_PAD_LEFT);

            // ขั้นตอนที่ 4: อัปเดตตาราง users ด้วยรหัสพนักงานที่สร้างขึ้น
            $stmt_update = $conn->prepare("UPDATE users SET employee_id = ? WHERE id = ?");
            $stmt_update->bind_param("si", $employee_id, $new_user_id);
            $stmt_update->execute();
            $stmt_update->close();
            
            $_SESSION['register_success'] = "การสมัครสมาชิกสำหรับพนักงานสำเร็จแล้ว! รหัสพนักงานของคุณคือ {$employee_id}";
        } else {
            $_SESSION['register_error'] = "เกิดข้อผิดพลาดในการสมัครสมาชิก: " . $conn->error;
        }
        $stmt_insert->close();
    }
    $stmt->close();
    $conn->close();
    
    // เปลี่ยนการ redirect ให้กลับไปหน้า register พร้อมพารามิเตอร์ form=employee
    header("Location: register.php?form=employee");
    exit;
}
?>