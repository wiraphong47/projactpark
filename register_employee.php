<?php session_start(); ?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครใช้งานสำหรับพนักงาน</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .login-container { max-width: 600px; }
        .form-group.select-group label { text-align: left; }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>ระบบจองที่จอดรถออนไลน์</h1>
        <h2>สมัครใช้งานสำหรับพนักงาน</h2>
        <form action="register_employee_process.php" method="post">
            <div class="form-group">
                <label for="full_name">ชื่อ-สกุล</label>
                <input type="text" id="full_name" name="full_name" required>
            </div>
            <div class="form-group">
                <label for="phone_number">เบอร์โทร</label>
                <input type="tel" id="phone_number" name="phone_number" required>
            </div>
            <div class="form-group">
                <label for="address">ที่อยู่</label>
                <textarea id="address" name="address" required></textarea>
            </div>
            <div class="form-group">
                <label for="username">ชื่อผู้ใช้</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">รหัสผ่าน</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group select-group">
                <label for="position">ตำแหน่ง</label>
                <select id="position" name="position" required>
                    <option value="employee">พนักงาน</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <?php
            $success_message = $_SESSION['register_success'] ?? '';
            $error_message = $_SESSION['register_error'] ?? '';
            unset($_SESSION['register_success'], $_SESSION['register_error']);
            
            if (!empty($success_message)) {
                echo '<p class="success-message">' . $success_message . '</p>';
            }
            if (!empty($error_message)) {
                echo '<p class="error-message">' . $error_message . '</p>';
            }
            ?>
            <button type="submit">สมัครใช้งาน</button>
        </form>
        <div class="extra-links">
            <a href="login.php">กลับสู่หน้า Login ผู้ใช้งาน</a>
            <a href="admin_login.php">เข้าสู่ระบบ Admin</a>
        </div>
    </div>
</body>
</html>