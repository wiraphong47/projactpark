<?php
session_start();
$success_message = $_SESSION['register_success'] ?? '';
$error_message = $_SESSION['register_error'] ?? '';
unset($_SESSION['register_success'], $_SESSION['register_error']);

// กำหนดค่าเริ่มต้นของฟอร์มที่จะแสดง
$form_to_show = $_GET['form'] ?? 'user';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครใช้งาน - ระบบจองที่จอดรถออนไลน์</title>
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
            background-color: #6403ffff;
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
        <h2>สมัครใช้งาน</h2>
        
        <div class="form-toggle">
            <button type="button" class="toggle-button" id="user-toggle">ผู้ใช้งาน</button>
            <button type="button" class="toggle-button" id="employee-toggle">พนักงาน</button>
        </div>

        <?php
        if (!empty($success_message)) {
            echo '<p class="success-message">' . $success_message . '</p>';
        }
        if (!empty($error_message)) {
            echo '<p class="error-message">' . $error_message . '</p>';
        }
        ?>

        <div id="user-form-section" class="form-section">
            <form action="register_process.php" method="post">
                <div class="form-group">
                    <label for="username">ชื่อผู้ใช้</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">รหัสผ่าน</label>
                    <input type="password" id="password" name="password" required>
                </div>
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
                <button type="submit">สมัครใช้งาน</button>
            </form>
        </div>

        <div id="employee-form-section" class="form-section">
            <form action="register_employee_process.php" method="post">
                <div class="form-group">
                    <label for="full_name_emp">ชื่อ-สกุล</label>
                    <input type="text" id="full_name_emp" name="full_name" required>
                </div>
                <div class="form-group">
                    <label for="phone_number_emp">เบอร์โทร</label>
                    <input type="tel" id="phone_number_emp" name="phone_number" required>
                </div>
                <div class="form-group">
                    <label for="address_emp">ที่อยู่</label>
                    <textarea id="address_emp" name="address" required></textarea>
                </div>
                <div class="form-group">
                    <label for="username_emp">ชื่อผู้ใช้</label>
                    <input type="text" id="username_emp" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password_emp">รหัสผ่าน</label>
                    <input type="password" id="password_emp" name="password" required>
                </div>
                <div class="form-group select-group">
                    <label for="position">ตำแหน่ง</label>
                    <select id="position" name="position" required>
                        <option value="employee">พนักงาน</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <button type="submit">สมัครใช้งาน</button>
            </form>
        </div>

        <div class="extra-links">
            <a href="login.php">กลับสู่หน้า Login</a>
            <a href="admin_login.php">สำหรับผู้ดูแลระบบ</a>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const userToggle = document.getElementById('user-toggle');
        const employeeToggle = document.getElementById('employee-toggle');
        const userForm = document.getElementById('user-form-section');
        const employeeForm = document.getElementById('employee-form-section');
        const initialForm = '<?php echo $form_to_show; ?>';

        function showForm(formType) {
            if (formType === 'user') {
                userForm.style.display = 'block';
                employeeForm.style.display = 'none';
                userToggle.classList.add('active');
                employeeToggle.classList.remove('active');
            } else {
                userForm.style.display = 'none';
                employeeForm.style.display = 'block';
                employeeToggle.classList.add('active');
                userToggle.classList.remove('active');
            }
        }

        userToggle.addEventListener('click', () => showForm('user'));
        employeeToggle.addEventListener('click', () => showForm('employee'));

        // แสดงฟอร์มที่ถูกต้องเมื่อโหลดหน้าครั้งแรกหรือหลังจาก redirect
        showForm(initialForm);
    });
    </script>
</body>
</html>