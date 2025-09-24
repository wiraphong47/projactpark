<?php
session_start();
// ตรวจสอบว่ามีข้อมูลใบเสร็จใน session หรือไม่
if (!isset($_SESSION['receipt_data'])) {
    header('Location: index.php');
    exit();
}

$receipt = $_SESSION['receipt_data'];

// ลบข้อมูลใบเสร็จออกจาก session เพื่อไม่ให้แสดงซ้ำเมื่อรีเฟรชหน้า
unset($_SESSION['receipt_data']);

// กำหนดโซนเวลาของประเทศไทย
date_default_timezone_set('Asia/Bangkok');
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ใบเสร็จรับเงิน</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #e9ecef;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            margin: 0;
            padding: 50px 20px;
        }
        .receipt-container {
            background-color: white;
            padding: 40px 50px;
            border-radius: 10px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 600px;
            border: 1px solid #ddd;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #007bff;
            font-size: 32px;
            margin: 0;
            font-weight: bold;
        }
        .header p {
            font-size: 14px;
            color: #6c757d;
            margin: 5px 0 0 0;
        }
        .info-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        .info-item {
            font-size: 16px;
            color: #343a40;
        }
        .info-item span {
            font-weight: bold;
            display: block;
            margin-top: 5px;
        }
        .info-item:nth-child(2) {
            text-align: right;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .details-table th, .details-table td {
            border: 1px solid #dee2e6;
            padding: 15px;
            text-align: left;
        }
        .details-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .total-section {
            text-align: right;
            font-size: 22px;
            font-weight: bold;
            color: #333;
            border-top: 2px solid #000;
            padding-top: 20px;
        }
        .thank-you {
            text-align: center;
            margin-top: 30px;
            font-style: italic;
            color: #6c757d;
        }
        .back-button-container {
            text-align: center;
            margin-top: 40px;
        }
        .back-button {
            display: inline-block;
            padding: 12px 25px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .back-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="header">
            <h1>ใบเสร็จรับเงิน</h1>
            <p>ระบบจองที่จอดรถออนไลน์</p>
        </div>
        
        <div class="info-section">
            <div class="info-item">
                หมายเลขใบเสร็จ: <span><?php echo uniqid('RCPT-'); ?></span>
            </div>
            <div class="info-item">
                วันที่: <span><?php echo date('d/m/Y'); ?></span>
            </div>
            <div class="info-item">
                ชื่อผู้ใช้: <span><?php echo htmlspecialchars($receipt['username']); ?></span>
            </div>
            <div class="info-item">
                เวลาที่ออก: <span><?php echo date('H:i น.'); ?></span>
            </div>
        </div>

        <table class="details-table">
            <thead>
                <tr>
                    <th>รายการ</th>
                    <th>รายละเอียด</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>สถานที่จอด</td>
                    <td><?php echo htmlspecialchars($receipt['spot']); ?></td>
                </tr>
                <tr>
                    <td>เวลาเข้า</td>
                    <td><?php echo date('d/m/Y H:i น.', strtotime($receipt['start_time'])); ?></td>
                </tr>
                <tr>
                    <td>เวลาออก</td>
                    <td><?php echo date('d/m/Y H:i น.', strtotime($receipt['end_time'])); ?></td>
                </tr>
            </tbody>
        </table>
        
        <div class="total-section">
            <span>รวมเป็นเงิน: <?php echo number_format($receipt['cost'], 2); ?> บาท</span>
        </div>
        
        <div class="thank-you">
            <p>ขอบคุณที่ใช้บริการของเรา</p>
        </div>
        
        <div class="back-button-container">
            <a href="index.php" class="back-button">กลับสู่หน้าหลัก</a>
        </div>
    </div>
</body>
</html>