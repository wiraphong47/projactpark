<?php
session_start();
require_once 'config.php';

// ตรวจสอบว่าเป็น Admin หรือไม่
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: admin_login.php');
    exit();
}

// ดึงข้อมูลโซนทั้งหมด
$zones = [];
$result = $conn->query("SELECT * FROM zones ORDER BY id");
if ($result) { while($row = $result->fetch_assoc()) { $zones[] = $row; } }

$conn->close();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการโซน - Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css"> <style>
        /* สไตล์เพิ่มเติมสำหรับหน้าจัดการ */
        .admin-container { max-width: 800px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .admin-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px; }
        .admin-header h1 { color: #007bff; font-size: 24px; }
        .add-form { margin-bottom: 20px; padding: 20px; background-color: #f8f9fa; border-radius: 8px; }
        .add-form input[type="text"] { width: 70%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; }
        .add-form button { padding: 10px 20px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .zone-table { width: 100%; border-collapse: collapse; }
        .zone-table th, .zone-table td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        .zone-table th { background-color: #f2f2f2; }
        .action-links a { color: #007bff; text-decoration: none; margin-right: 10px; }
        .action-links a.delete { color: #dc3545; }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>จัดการโซนที่จอดรถ</h1>
            <a href="admin_dashboard.php">กลับ Dashboard</a>
        </div>

        <div class="add-form">
            <h2>เพิ่มโซนใหม่</h2>
            <form action="zone_process.php" method="POST">
                <input type="text" name="zone_name" placeholder="ชื่อโซน" required>
                <button type="submit" name="add_zone">เพิ่มโซน</button>
            </form>
        </div>

        <h2>โซนทั้งหมด</h2>
        <table class="zone-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ชื่อโซน</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($zones as $zone): ?>
                <tr>
                    <td><?php echo $zone['id']; ?></td>
                    <td><?php echo htmlspecialchars($zone['name']); ?></td>
                    <td class="action-links">
                        <a href="zone_process.php?delete_zone=<?php echo $zone['id']; ?>" class="delete" onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบโซนนี้?');">ลบ</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>