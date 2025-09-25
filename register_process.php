<?php
session_start();
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // ตรวจสอบว่าชื่อผู้ใช้มีอยู่ในระบบแล้วหรือไม่
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['register_error'] = "ชื่อผู้ใช้ '{$username}' มีอยู่ในระบบแล้ว กรุณาเลือกชื่ออื่น!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // ขั้นตอนที่ 1: บันทึกข้อมูลเบื้องต้น (ไม่รวม member_id)
        $stmt_insert = $conn->prepare("INSERT INTO users (username, password, full_name, phone_number, address) VALUES (?, ?, ?, ?, ?)");
        $stmt_insert->bind_param("sssss", $username, $hashed_password, $full_name, $phone_number, $address);

        if ($stmt_insert->execute()) {
            
            // ขั้นตอนที่ 2: ดึง id ที่สร้างขึ้นอัตโนมัติ
            $new_user_id = $conn->insert_id;

            // ขั้นตอนที่ 3: สร้างรหัสสมาชิกในรูปแบบ AXX
            $member_id = 'A' . str_pad($new_user_id, 2, '0', STR_PAD_LEFT);

            // ขั้นตอนที่ 4: อัปเดตตาราง users ด้วยรหัสสมาชิกที่สร้างขึ้น
            $stmt_update = $conn->prepare("UPDATE users SET member_id = ? WHERE id = ?");
            $stmt_update->bind_param("si", $member_id, $new_user_id);
            $stmt_update->execute();
            $stmt_update->close();
            
            $_SESSION['register_success'] = "การสมัครสมาชิกสำเร็จแล้ว! คุณสามารถเข้าสู่ระบบได้เลย";
        } else {
            $_SESSION['register_error'] = "เกิดข้อผิดพลาดในการสมัครสมาชิก: " . $conn->error;
        }
        $stmt_insert->close();
    }
    $stmt->close();
    $conn->close();

    // เปลี่ยนการ redirect ให้กลับไปหน้า register พร้อมพารามิเตอร์ form=user
    header("Location: register.php?form=user");
    exit;
}
?>