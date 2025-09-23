<?php
session_start();
$error_message = '';
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจองที่จอดรถออนไลน์</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <h1>ระบบจองที่จอดรถออนไลน์</h1>
        <h2>Login</h2>
        <form action="login_process.php" method="post">
            <div class="form-group">
                <label for="username">ชื่อ</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">รหัส</label>
                <input type="password" id="password" name="password" required>
            </div>
            <?php if (!empty($error_message)) { echo '<p class="error-message">' . $error_message . '</p>'; } ?>
            <button type="submit">เข้าสู่ระบบ</button>
        </form>
        <div class="extra-links">
            <a href="#">ลืมรหัสผ่าน?</a>
            <a href="register.php">สมัครใช้งาน</a>
        </div>
    </div>
</body>
</html>