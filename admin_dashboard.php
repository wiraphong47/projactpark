<?php
session_start();
require_once 'config.php';

// ตรวจสอบว่าเป็น Admin หรือ Employee หรือไม่
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'employee')) {
    header('Location: admin_login.php?error=กรุณาเข้าสู่ระบบสำหรับผู้ดูแล');
    exit();
}

// ดึงข้อมูลโซนทั้งหมด
$zones = [];
$zone_result = $conn->query("SELECT id, name FROM zones ORDER BY id");
if ($zone_result) { while($row = $zone_result->fetch_assoc()) { $zones[] = $row; } }

// ดึงข้อมูลที่จอดรถตามโซน
$spots_by_zone = [];
if (!empty($zones)) {
    foreach ($zones as $zone) {
        $stmt = $conn->prepare("SELECT spot_name, status, booked_by_user FROM parking_spots WHERE zone_id = ? ORDER BY id");
        $stmt->bind_param("i", $zone['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $spots = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $spots[] = $row;
            }
        }
        $spots_by_zone[$zone['id']] = $spots;
        $stmt->close();
    }
}

// ดึงข้อมูลพนักงานและประวัติการเข้าสู่ระบบ (สำหรับ Admin เท่านั้น)
$employees = [];
if ($_SESSION['role'] === 'admin') {
    $sql = "SELECT u.full_name, u.employee_id, u.phone_number, u.address,
            lh.login_time, lh.logout_time
            FROM users u
            LEFT JOIN (
                SELECT user_id, MAX(login_time) as login_time, MAX(logout_time) as logout_time
                FROM login_history
                GROUP BY user_id
            ) lh ON u.id = lh.user_id
            WHERE u.role = 'employee' OR u.role = 'admin'
            ORDER BY u.full_name ASC";
    $result_employees = $conn->query($sql);
    if ($result_employees) {
        while($row = $result_employees->fetch_assoc()) {
            $employees[] = $row;
        }
    }
}

// ดึงข้อมูลพนักงานที่ล็อกอินอยู่ (สำหรับพนักงาน)
$current_employee_info = null;
if (isset($_SESSION['username'])) {
    $username_logged_in = $_SESSION['username'];
    $sql_employee = "SELECT u.full_name, u.employee_id, u.phone_number, u.address,
                     lh.login_time, lh.logout_time
                     FROM users u
                     LEFT JOIN (
                         SELECT user_id, MAX(login_time) as login_time, MAX(logout_time) as logout_time
                         FROM login_history
                         GROUP BY user_id
                     ) lh ON u.id = lh.user_id
                     WHERE u.username = ?";
    $stmt_employee = $conn->prepare($sql_employee);
    $stmt_employee->bind_param("s", $username_logged_in);
    $stmt_employee->execute();
    $result_employee = $stmt_employee->get_result();
    if ($result_employee->num_rows > 0) {
        $current_employee_info = $result_employee->fetch_assoc();
    }
    $stmt_employee->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;700&display=swap" rel="stylesheet">
    <style>
       /* === General Styles === */
body {
    font-family: 'Kanit', sans-serif;
    background-color: #e9ecef; /* Lighter, modern background */
    color: #495057;
    margin: 0;
    padding: 0;
}

/* === Main Container === */
.container {
    max-width: 1000px;
    margin: 40px auto;
    background-color: #ffffff;
    padding: 40px;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

/* === Header Section === */
.header-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 2px solid #ced4da;
    padding-bottom: 20px;
    margin-bottom: 30px;
}

.header-section h1 {
    font-size: 32px;
    color: #0056b3; /* Darker blue for a professional look */
    margin: 0;
    font-weight: 700;
}

.header-section a.logout-btn {
    background-color: #dc3545;
    color: white;
    padding: 10px 20px;
    border-radius: 25px;
    text-decoration: none;
    font-size: 15px;
    font-weight: 500;
    transition: background-color 0.3s, transform 0.2s;
}

.header-section a.logout-btn:hover {
    background-color: #c82333;
    transform: translateY(-2px);
}

.welcome-message {
    margin-bottom: 30px;
    font-size: 18px;
    color: #6c757d;
    font-weight: 400;
}

/* === Navigation/Action Links === */
.action-links {
    margin-bottom: 25px;
    display: flex;
    gap: 15px;
}

.action-links a {
    padding: 12px 20px;
    background-color: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 500;
    transition: background-color 0.3s, transform 0.2s;
}

.action-links a:hover {
    background-color: #0056b3;
    transform: translateY(-2px);
}

/* === Parking Grid === */
.parking-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 25px;
    padding: 10px;
}

.spot {
    height: 100px;
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    font-weight: bold;
    color: white;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    position: relative;
    text-align: center;
    padding: 10px;
}

.spot:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.spot.available {
    background-color: #4CAF50; /* Green for available */
}

.spot.occupied {
    background-color: #EF5350; /* Red for occupied */
}

.spot-name {
    font-size: 18px;
    margin-bottom: 8px;
    font-weight: 700;
}

.booked-by {
    font-size: 12px;
    font-weight: normal;
    opacity: 0.9;
}

/* === Reset Button === */
.reset-button {
    background-color: #FFC107;
    color: #343a40;
    border: none;
    padding: 8px 16px;
    border-radius: 20px;
    cursor: pointer;
    font-size: 14px;
    font-weight: bold;
    margin-top: 10px;
    transition: background-color 0.3s, transform 0.2s;
}

.reset-button:hover {
    background-color: #E0A800;
    transform: translateY(-2px);
}

/* === Responsive adjustments === */
@media (max-width: 768px) {
    .container {
        padding: 20px;
        margin: 20px;
    }

    .parking-grid {
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    }
}
.employee-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    font-size: 14px;
}

.employee-table th, .employee-table td {
    border: 1px solid #ddd;
    padding: 12px;
    text-align: left;
}

.employee-table th {
    background-color: #f2f2f2;
}

.employee-table tr:nth-child(even) {
    background-color: #f9f9f9;
}
.user-info-box {
    background-color: #f0f8ff; /* Light blue background */
    border: 1px solid #cce5ff;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 30px;
    line-height: 1.8;
}
    </style>
</head>
<body>
    <div class="container">
        <div class="header-section">
            <h1>แผงควบคุมผู้ดูแลระบบ</h1>
            <a href="logout.php">ออกจากระบบ</a>
        </div>
        <p class="welcome-message">ยินดีต้อนรับ, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></p>
        
        <?php if ($_SESSION['role'] === 'admin'): ?>
        <div style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #eee;">
            <a href="manage_zones.php" style="padding: 10px 15px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;">จัดการโซน</a>
            <a href="employee_login_history.php" style="padding: 10px 15px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px;">ดูประวัติการเข้าสู่ระบบ</a>
        </div>
        
        <div id="employee-data-container">
            </div>

        <?php elseif ($_SESSION['role'] === 'employee'): ?>
        <h2 style="margin-top: 40px;">ข้อมูลของคุณ</h2>
        <div class="user-info-box">
            <?php if ($current_employee_info): ?>
                <p><strong>ชื่อ-สกุล:</strong> <?php echo htmlspecialchars($current_employee_info['full_name']); ?></p>
                <p><strong>รหัสพนักงาน:</strong> <?php echo htmlspecialchars($current_employee_info['employee_id'] ?? '-'); ?></p>
                <p><strong>เบอร์โทร:</strong> <?php echo htmlspecialchars($current_employee_info['phone_number']); ?></p>
                <p><strong>ที่อยู่:</strong> <?php echo htmlspecialchars($current_employee_info['address']); ?></p>
                <p><strong>เวลาเข้างานล่าสุด:</strong> <?php echo htmlspecialchars($current_employee_info['login_time'] ? date('d-m-Y H:i:s', strtotime($current_employee_info['login_time'])) : '-'); ?></p>
                <p><strong>เวลาออกงานล่าสุด:</strong> <?php echo htmlspecialchars($current_employee_info['logout_time'] ? date('d-m-Y H:i:s', strtotime($current_employee_info['logout_time'])) : '-'); ?></p>
            <?php else: ?>
                <p>ไม่พบข้อมูลของคุณ</p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <h2 style="margin-top: 40px;">สถานะที่จอดรถ</h2>
        <?php if (!empty($zones)): ?>
            <?php foreach ($zones as $zone): ?>
                <div class="zone-section" style="margin-bottom: 40px;">
                    <h2 style="font-size: 24px; border-bottom: 1px solid #ccc; padding-bottom: 10px;"><?php echo htmlspecialchars($zone['name']); ?></h2>
                    <div class="parking-grid">
                        <?php if (!empty($spots_by_zone[$zone['id']])): ?>
                            <?php foreach ($spots_by_zone[$zone['id']] as $spot): ?>
                                <div class="spot <?php echo $spot['status']; ?>">
                                    <span class="spot-name"><?php echo $spot['spot_name']; ?></span>
                                    <?php if ($spot['status'] === 'occupied'): ?>
                                        <span class="booked-by">จองโดย: <?php echo htmlspecialchars($spot['booked_by_user']); ?></span>
                                        <?php if ($_SESSION['role'] === 'admin'): ?>
                                        <form action="admin_reset_spot.php" method="POST" style="margin:0;">
                                            <input type="hidden" name="spot_name" value="<?php echo $spot['spot_name']; ?>">
                                            <button type="submit" class="reset-button">รีเซ็ต</button>
                                        </form>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p style="text-align: center; grid-column: 1 / -1;">ไม่พบที่จอดรถในโซนนี้</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center;">ไม่พบโซนที่จอดรถ</p>
        <?php endif; ?>
    </div>

    <script>
    function fetchEmployeeData() {
        fetch('get_employee_data.php')
            .then(response => response.text())
            .then(html => {
                const container = document.getElementById('employee-data-container');
                if (container) {
                    container.innerHTML = html;
                }
            })
            .catch(error => console.error('Error fetching employee data:', error));
    }

    // โหลดข้อมูลครั้งแรกสำหรับ Admin
    if ('<?php echo $_SESSION['role']; ?>' === 'admin') {
        fetchEmployeeData();
        // ตั้งเวลาโหลดข้อมูลใหม่ทุก 5 นาที (300000 มิลลิวินาที)
        setInterval(fetchEmployeeData, 300000);
    }
    </script>
</body>
</html>