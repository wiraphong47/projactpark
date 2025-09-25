<?php session_start(); ?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Employee Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <h1>สำหรับพนักงาน</h1>
        <h2>Employee Login</h2>
        <form action="login_employee_process.php" method="post">
            <div class="form-group">
                <label for="username">ชื่อผู้ใช้</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">รหัสผ่าน</label>
                <input type="password" id="password" name="password" required>
            </div>
            <?php 
                if (isset($_GET['error'])) {
                    echo '<p class="error-message">' . htmlspecialchars($_GET['error']) . '</p>';
                }
            ?>
            <button type="submit">เข้าสู่ระบบ</button>
        </form>
        <div class="extra-links">
            <a href="login.php">กลับสู่หน้าผู้ใช้งาน</a>
            <a href="admin_login.php">สำหรับผู้ดูแลระบบ</a>
        </div>
    </div>
</body>
</html>