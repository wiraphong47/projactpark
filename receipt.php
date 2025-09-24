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
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0056b3;
            --secondary-color: #007bff;
            --background-color: #f0f2f5;
            --text-color-dark: #2c3e50;
            --text-color-light: #7f8c8d;
            --border-color: #e0e6ed;
            --receipt-bg: #ffffff;
        }

        body {
            font-family: 'Kanit', sans-serif;
            background-color: var(--background-color);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 30px 20px;
        }

        .receipt-container {
            background-color: var(--receipt-bg);
            padding: 50px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            width: 100%;
            max-width: 650px;
            border: 1px solid var(--border-color);
        }
        
        /* Header Section */
        .receipt-header {
            text-align: center;
            border-bottom: 3px solid var(--primary-color);
            padding-bottom: 25px;
            margin-bottom: 30px;
        }
        .receipt-header h1 {
            color: var(--primary-color);
            font-size: 36px;
            margin: 0;
            font-weight: 700;
        }
        .receipt-header p {
            font-size: 16px;
            color: var(--text-color-light);
            margin: 8px 0 0 0;
        }

        /* Info Section */
        .info-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 40px;
        }
        .info-item {
            font-size: 16px;
            color: var(--text-color-dark);
        }
        .info-item .label {
            font-weight: 300;
            color: var(--text-color-light);
            display: block;
            margin-bottom: 4px;
        }
        .info-item .value {
            font-weight: 400;
            font-size: 18px;
        }
        .info-item:nth-child(2),
        .info-item:nth-child(4) {
            text-align: right;
        }

        /* Details Table */
        .details-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 40px;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid var(--border-color);
        }
        .details-table th, .details-table td {
            padding: 18px;
            text-align: left;
            font-size: 16px;
        }
        .details-table th {
            background-color: var(--background-color);
            color: var(--text-color-dark);
            font-weight: 700;
            border-bottom: 1px solid var(--border-color);
        }
        .details-table td {
            border-bottom: 1px solid var(--border-color);
            color: var(--text-color-dark);
        }
        .details-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Total Section */
        .total-section {
            display: flex;
            justify-content: flex-end;
            align-items: baseline;
            gap: 15px;
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-color);
            border-top: 3px solid var(--primary-color);
            padding-top: 25px;
        }
        .total-section .label {
            font-size: 20px;
            color: var(--text-color-dark);
        }
        .total-section .amount {
            font-size: 28px;
            color: var(--primary-color);
        }

        /* Footer and Buttons */
        .receipt-footer {
            text-align: center;
            margin-top: 40px;
        }
        .thank-you {
            font-style: italic;
            color: var(--text-color-light);
            margin-bottom: 25px;
        }
        .back-button {
            display: inline-block;
            padding: 15px 30px;
            background-color: var(--secondary-color);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-size: 18px;
            font-weight: 400;
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.2);
        }
        .back-button:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }
        .back-button:active {
            transform: translateY(0);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .receipt-container {
                padding: 30px;
            }
            .info-section {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            .info-item:nth-child(2),
            .info-item:nth-child(4) {
                text-align: left;
            }
            .total-section {
                font-size: 20px;
            }
            .total-section .amount {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <header class="receipt-header">
            <h1>ใบเสร็จรับเงิน</h1>
            <p>ระบบจองที่จอดรถออนไลน์</p>
        </header>
        
        <section class="info-section">
            <div class="info-item">
                <span class="label">หมายเลขใบเสร็จ</span>
                <span class="value"><?php echo uniqid('RCPT-'); ?></span>
            </div>
            <div class="info-item">
                <span class="label">วันที่</span>
                <span class="value"><?php echo date('d/m/Y'); ?></span>
            </div>
            <div class="info-item">
                <span class="label">ชื่อผู้ใช้</span>
                <span class="value"><?php echo htmlspecialchars($receipt['username']); ?></span>
            </div>
            <div class="info-item">
                <span class="label">เวลาที่ออก</span>
                <span class="value"><?php echo date('H:i น.'); ?></span>
            </div>
        </section>

        <table class="details-table">
            <thead>
                <tr>
                    <th>รายการ</th>
                    <th>รายละเอียด</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>สถานที่</td>
                    <td><?php echo htmlspecialchars($receipt['zone_name']); ?></td>
                </tr>
                <tr>
                    <td>ที่จอด</td>
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
        
        <section class="total-section">
            <span class="label">รวมเป็นเงิน:</span>
            <span class="amount"><?php echo number_format($receipt['cost'], 2); ?> บาท</span>
        </section>
        
        <footer class="receipt-footer">
            <div class="thank-you">
                <p>ขอบคุณที่ใช้บริการของเรา</p>
            </div>
            <div class="back-button-container">
                <a href="index.php" class="back-button">กลับสู่หน้าหลัก</a>
            </div>
        </footer>
    </div>
</body>
</html>