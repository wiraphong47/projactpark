<?php
session_start();
$success_message = '';
$error_message = '';
if (isset($_SESSION['register_success'])) {
    $success_message = $_SESSION['register_success'];
    unset($_SESSION['register_success']);
}
if (isset($_SESSION['register_error'])) {
    $error_message = $_SESSION['register_error'];
    unset($_SESSION['register_error']);
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครใช้งาน - ระบบจองที่จอดรถออนไลน์</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <h1>ระบบจองที่จอดรถออนไลน์</h1>
        <h2>สมัครใช้งาน</h2>
        <form action="register_process.php" method="post">
            <div class="form-group">
                <label for="username">ชื่อผู้ใช้</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">รหัสผ่าน</label>
                <input type="password" id="password" name="password" required>
            </div>
            <?php
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
            <a href="index.php">กลับสู่หน้า Login</a>
        </div>
    </div>
</body>
</html>