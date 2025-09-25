<?php
session_start();
$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['error_message']);

// กำหนดค่าเริ่มต้นของฟอร์มที่จะแสดง
$form_to_show = $_GET['form'] ?? 'member';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจองที่จอดรถออนไลน์</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .login-container { max-width: 600px; }
        .form-toggle {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
            gap: 10px;
        }
        .toggle-button {
            padding: 10px 20px;
            border: 1px solid #000000ff;
            background-color: #ff00f2ff;
            cursor: pointer;
            font-family: 'Kanit', sans-serif;
            border-radius: 8px;
            transition: background-color 0.3s ease;
            font-weight: 500;
        }
        .toggle-button.active {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
            box-shadow: 0 2px 5px rgba(0, 123, 255, 0.2);
        }
        .form-section {
            display: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>ระบบจองที่จอดรถออนไลน์</h1>
        <h2>Login</h2>

        <div class="form-toggle">
            <button type="button" class="toggle-button" id="member-toggle">สมาชิก</button>
            <button type="button" class="toggle-button" id="employee-toggle">พนักงาน</button>
        </div>
        
        <?php if (!empty($error_message)) { echo '<p class="error-message">' . $error_message . '</p>'; } ?>
        
        <div id="member-form-section" class="form-section">
            <form action="login_process.php" method="post">
                <div class="form-group">
                    <label for="username">ชื่อผู้ใช้</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">รหัสผ่าน</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit">เข้าสู่ระบบ</button>
            </form>
        </div>

        <div id="employee-form-section" class="form-section">
            <form action="login_employee_process.php" method="post">
                <div class="form-group">
                    <label for="username_emp">ชื่อผู้ใช้</label>
                    <input type="text" id="username_emp" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password_emp">รหัสผ่าน</label>
                    <input type="password" id="password_emp" name="password" required>
                </div>
                <button type="submit">เข้าสู่ระบบ</button>
            </form>
        </div>

        <div class="extra-links">
            <a href="forgot_password.php">ลืมรหัสผ่าน?</a>
            <a href="register.php">สมัครใช้งาน</a>
            <a href="admin_login.php">สำหรับผู้ดูแลระบบ</a>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const memberToggle = document.getElementById('member-toggle');
        const employeeToggle = document.getElementById('employee-toggle');
        const memberForm = document.getElementById('member-form-section');
        const employeeForm = document.getElementById('employee-form-section');
        const initialForm = '<?php echo $form_to_show; ?>';

        function showForm(formType) {
            if (formType === 'member') {
                memberForm.style.display = 'block';
                employeeForm.style.display = 'none';
                memberToggle.classList.add('active');
                employeeToggle.classList.remove('active');
            } else {
                memberForm.style.display = 'none';
                employeeForm.style.display = 'block';
                employeeToggle.classList.add('active');
                memberToggle.classList.remove('active');
            }
        }

        memberToggle.addEventListener('click', () => showForm('member'));
        employeeToggle.addEventListener('click', () => showForm('employee'));

        // แสดงฟอร์มที่ถูกต้องเมื่อโหลดหน้าครั้งแรกหรือหลังจาก redirect
        showForm(initialForm);
    });
    </script>
</body>
</html>