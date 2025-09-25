<?php
session_start();
require_once 'config.php';

// ตรวจสอบสิทธิ์การเข้าถึง (เฉพาะ Admin เท่านั้น)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    exit('Access Denied');
}

// ดึงข้อมูลพนักงานและประวัติการเข้าสู่ระบบล่าสุด
$employees = [];
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
$conn->close();

// แสดงผลลัพธ์เป็น HTML
?>
<h2 style="margin-top: 40px;">ข้อมูลพนักงาน</h2>
<?php if (!empty($employees)): ?>
    <table class="employee-table">
        <thead>
            <tr>
                <th>รหัสพนักงาน</th>
                <th>ชื่อ-สกุล</th>
                <th>เบอร์โทร</th>
                <th>ที่อยู่</th>
                <th>เวลาเข้างานล่าสุด</th>
                <th>เวลาออกงานล่าสุด</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($employees as $emp): ?>
            <tr>
                <td><?php echo htmlspecialchars($emp['employee_id'] ?? '-'); ?></td>
                <td><?php echo htmlspecialchars($emp['full_name']); ?></td>
                <td><?php echo htmlspecialchars($emp['phone_number']); ?></td>
                <td><?php echo htmlspecialchars($emp['address']); ?></td>
                <td><?php echo htmlspecialchars($emp['login_time'] ? date('d-m-Y H:i:s', strtotime($emp['login_time'])) : '-'); ?></td>
                <td><?php echo htmlspecialchars($emp['logout_time'] ? date('d-m-Y H:i:s', strtotime($emp['logout_time'])) : '-'); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>ไม่พบข้อมูลพนักงาน</p>
<?php endif; ?>