<?php
session_start();
require_once 'config.php';

// ตรวจสอบว่าเป็น Admin เท่านั้นที่เข้าถึงหน้านี้ได้
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: admin_login.php?error=คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
    exit();
}

// ดึงข้อมูลประวัติการล็อกอินของพนักงานทั้งหมด
$history = [];
$stmt = $conn->prepare("SELECT u.username, u.full_name, lh.login_time, lh.logout_time FROM login_history lh JOIN users u ON lh.user_id = u.id WHERE u.role = 'employee' ORDER BY lh.login_time DESC");
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $history[] = $row;
    }
}
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ประวัติการเข้าสู่ระบบของพนักงาน</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .container { max-width: 900px; }
        .history-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .history-table th, .history-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .history-table th {
            background-color: #f2f2f2;
        }
        .history-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 style="text-align: center;">ประวัติการเข้าสู่ระบบของพนักงาน</h1>
        <a href="admin_dashboard.php" class="back-button">กลับสู่หน้า Dashboard</a>

        <?php if (!empty($history)): ?>
        <table class="history-table">
            <thead>
                <tr>
                    <th>ชื่อผู้ใช้</th>
                    <th>ชื่อ-สกุล</th>
                    <th>เวลาเข้าสู่ระบบ</th>
                    <th>เวลาออกจากระบบ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($history as $record): ?>
                <tr>
                    <td><?php echo htmlspecialchars($record['username']); ?></td>
                    <td><?php echo htmlspecialchars($record['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($record['login_time']); ?></td>
                    <td><?php echo htmlspecialchars($record['logout_time'] ?? 'ยังไม่ได้ออกจากระบบ'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p style="text-align: center;">ยังไม่มีประวัติการเข้าสู่ระบบของพนักงาน</p>
        <?php endif; ?>
    </div>
</body>
</html>