<?php
session_start();
$success_message = '';
$error_message = '';
if (isset($_SESSION['reset_success'])) {
    $success_message = $_SESSION['reset_success'];
    unset($_SESSION['reset_success']);
}
if (isset($_SESSION['reset_error'])) {
    $error_message = $_SESSION['reset_error'];
    unset($_SESSION['reset_error']);
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ลืมรหัสผ่าน - ระบบจองที่จอดรถออนไลน์</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <h1>ระบบจองที่จอดรถออนไลน์</h1>
        <h2>ลืมรหัสผ่าน?</h2>
        <form action="forgot_password_process.php" method="post">
            <div class="form-group">
                <label for="username_email">ชื่อผู้ใช้/อีเมล</label>
                <input type="text" id="username_email" name="username_email" required>
            </div>
            
            <?php
            if (!empty($success_message)) {
                echo '<p class="success-message">' . $success_message . '</p>';
            }
            if (!empty($error_message)) {
                echo '<p class="error-message">' . $error_message . '</p>';
            }
            ?>
            
            <button type="submit">ส่งคำขอ</button>
        </form>
        <div class="extra-links">
            <a href="login.php">กลับสู่หน้า Login</a>
        </div>
    </div>
</body>
</html>