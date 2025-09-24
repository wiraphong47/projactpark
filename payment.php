<?php
session_start();
require_once 'config.php';

// Function to calculate service cost
function calculate_cost($start, $end) {
    $start_datetime = new DateTime($start);
    $end_datetime = new DateTime($end);
    $interval = $start_datetime->diff($end_datetime);
    $hours = $interval->h;
    $days = $interval->d;
    $minutes = $interval->i;
    if ($minutes > 0) {
        $hours += 1;
    }
    $total_hours = ($days * 24) + $hours;
    return $total_hours * 50; 
}

// --- Section 1: Handle slip upload and booking confirmation ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_booking'])) {
    
    $booked_spot = $_POST['booked_spot'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $total_cost = calculate_cost($start_time, $end_time);
    $username = $_SESSION['username'] ?? 'guest';
    $payment_method = $_POST['payment_method'];
    $is_booking_success = false;

    // --- Check payment method ---
    if ($payment_method === 'qr_code') {
        // Original code for QR Code (slip upload)
        $is_upload_success = false;
        if (isset($_FILES['payment_slip']) && $_FILES['payment_slip']['error'] === UPLOAD_ERR_OK) {
            $file_tmp_path = $_FILES['payment_slip']['tmp_name'];
            $file_name = $_FILES['payment_slip']['name'];
            $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $new_file_name = uniqid('slip_', true) . '.' . $file_extension;
            $upload_dir = 'uploads/';
            $dest_path = $upload_dir . $new_file_name;
            if (move_uploaded_file($file_tmp_path, $dest_path)) {
                $is_upload_success = true;
            }
        }
        if ($is_upload_success) {
            $stmt = $conn->prepare("UPDATE parking_spots SET status = 'occupied', booked_by_user = ? WHERE spot_name = ? AND status = 'available'");
            $stmt->bind_param("ss", $username, $booked_spot);
            $stmt->execute();
            $is_booking_success = $stmt->affected_rows > 0;
            $stmt->close();
        }

    } elseif ($payment_method === 'credit_card') {
        // Simulated code for credit card processing
        $is_payment_successful = true; // Assume success
        if ($is_payment_successful) {
            $stmt = $conn->prepare("UPDATE parking_spots SET status = 'occupied', booked_by_user = ? WHERE spot_name = ? AND status = 'available'");
            $stmt->bind_param("ss", $username, $booked_spot);
            $stmt->execute();
            $is_booking_success = $stmt->affected_rows > 0;
            $stmt->close();
        }
    }
    
    $conn->close();

    // If booking is successful, store data in session and redirect to receipt page
    if ($is_booking_success) {
        $_SESSION['receipt_data'] = [
            'spot' => $booked_spot,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'cost' => $total_cost,
            'username' => $username,
            'transaction_date' => date('Y-m-d H:i:s'),
            'payment_method' => $payment_method
        ];
        header('Location: receipt.php');
        exit();
    }
    // If not successful, display an error page
    echo '
    <!DOCTYPE html>
    <html lang="th">
    <head>
        <meta charset="UTF-8">
        <title>สถานะการจอง</title>
        <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;700&display=swap" rel="stylesheet">
        <style>
            body { font-family: "Kanit", sans-serif; background-color: #f4f4f9; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
            .container { background-color: white; padding: 40px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); text-align: center; max-width: 500px; }
            h1 { font-size: 28px; margin-bottom: 15px; }
            p { font-size: 18px; color: #555; }
            .back-button { display: inline-block; margin-top: 25px; padding: 12px 25px; background-color: #007bff; color: white; text-decoration: none; border-radius: 25px; font-size: 16px; transition: background-color 0.3s; }
            .back-button:hover { background-color: #0056b3; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1 style="color: #dc3545;">เกิดข้อผิดพลาด!</h1>
            <p>ขออภัย ที่จอด <strong>' . htmlspecialchars($booked_spot) . '</strong> อาจถูกจองไปแล้ว หรือเกิดปัญหาในการอัปโหลดสลิป</p>
            <a href="index.php" class="back-button">กลับสู่หน้าหลัก</a>
        </div>
    </body>
    </html>';

    exit(); 
}

// --- Section 2: Display booking confirmation page (when coming from index or park) ---
if (isset($_POST['selected_spot']) && !empty($_POST['selected_spot'])) {
    $selected_spot = htmlspecialchars($_POST['selected_spot']);
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $total_cost = calculate_cost($start_time, $end_time);
} else {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ยืนยันและชำระเงิน</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px 0;
            color: #333;
        }
        .container {
            background-color: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 550px;
            text-align: center;
            border: 1px solid #e0e6ed;
            animation: fadeIn 0.8s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        h1 {
            font-size: 32px;
            color: #2c3e50;
            margin-bottom: 10px;
            font-weight: 700;
        }
        p.subtitle {
            font-size: 16px;
            color: #7f8c8d;
            margin-bottom: 30px;
        }
        .info-card {
            background-color: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: inset 0 2px 5px rgba(0,0,0,0.05);
            text-align: left;
        }
        .info-card h2 {
            font-size: 24px;
            color: #34495e;
            margin-bottom: 15px;
            font-weight: 700;
            display: flex;
            align-items: center;
        }
        .info-card h2 .icon {
            font-size: 28px;
            color: #3498db;
            margin-right: 10px;
        }
        .info-card p {
            font-size: 18px;
            color: #555;
            line-height: 1.8;
            margin: 0;
        }
        .info-card strong {
            color: #2980b9;
            font-size: 22px;
            font-weight: 700;
        }
        .info-card .cost {
            font-size: 30px;
            font-weight: 700;
            color: #e74c3c;
            margin-top: 15px;
            text-align: right;
        }
        .payment-method-selector {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 30px;
            margin-bottom: 30px;
        }
        .method-btn {
            padding: 12px 30px;
            border-radius: 30px;
            border: 2px solid #bdc3c7;
            background-color: white;
            color: #7f8c8d;
            font-family: 'Kanit', sans-serif;
            font-size: 18px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .method-btn.active,
        .method-btn:hover {
            background-color: #3498db;
            color: white;
            border-color: #3498db;
            transform: translateY(-3px);
            box-shadow: 0 4px 10px rgba(52, 152, 219, 0.3);
        }
        .payment-section {
            border-top: 1px solid #e0e6ed;
            padding-top: 30px;
            text-align: center;
        }
        .payment-section h2 {
            font-size: 24px;
            color: #34495e;
            margin-bottom: 20px;
            font-weight: 700;
        }
        .qr-code {
            max-width: 250px;
            width: 100%;
            height: auto;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .slip-upload-label {
            display: block;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #34495e;
        }
        #payment_slip {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 25px;
            background-color: #f9f9f9;
        }
        .confirm-button {
            width: 100%;
            padding: 18px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(90deg, #2ecc71 0%, #27ae60 100%);
            color: white;
            font-family: 'Kanit', sans-serif;
            font-size: 22px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(46, 204, 113, 0.4);
        }
        .confirm-button:hover {
            background: linear-gradient(90deg, #27ae60 0%, #2ecc71 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(46, 204, 113, 0.5);
        }
        .payment-form-section {
            display: none;
            animation: fadeIn 0.6s ease-in-out;
        }
        .payment-form-section.active {
            display: block;
        }
        .credit-card-form .form-group {
            text-align: left;
            margin-bottom: 18px;
        }
        .credit-card-form .form-group label {
            display: block;
            font-size: 16px;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }
        .credit-card-form .form-group input {
            width: 95%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fcfcfc;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);
            transition: border-color 0.3s;
        }
        .credit-card-form .form-group input:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.5);
        }
        .credit-card-form .form-row {
            display: flex;
            gap: 15px;
        }
        .credit-card-form .form-row .form-group {
            flex: 1;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>ยืนยันและชำระเงิน</h1>
        <p class="subtitle">กรุณาตรวจสอบรายละเอียดการจองและเลือกช่องทางการชำระเงิน</p>

        <div class="info-card">
            <h2><i class="fas fa-info-circle icon"></i> รายละเอียดการจอง</h2>
            <p>ที่จอด: <strong><?php echo $selected_spot; ?></strong></p>
            <p>เวลาเข้า: <?php echo date('d/m/Y H:i', strtotime($start_time)); ?> น.</p>
            <p>เวลาออก: <?php echo date('d/m/Y H:i', strtotime($end_time)); ?> น.</p>
            <p class="cost">ค่าบริการ: <strong><?php echo number_format($total_cost, 2); ?> บาท</strong></p>
        </div>

        <div class="payment-method-selector">
            <button type="button" class="method-btn active" data-method="qr_code"><i class="fas fa-qrcode"></i> QR Code</button>
            <button type="button" class="method-btn" data-method="credit_card"><i class="fas fa-credit-card"></i> บัตรเครดิต</button>
        </div>

        <div class="payment-section">
            <form action="payment.php" method="POST" enctype="multipart/form-data" id="qr_code-form" class="payment-form-section active">
                <h2>ชำระเงินผ่าน QR Code</h2>
                <img src="qr_code.png" alt="QR Code สำหรับชำระเงิน" class="qr-code">
                
                <input type="hidden" name="payment_method" value="qr_code">
                <input type="hidden" name="booked_spot" value="<?php echo $selected_spot; ?>">
                <input type="hidden" name="start_time" value="<?php echo htmlspecialchars($start_time); ?>">
                <input type="hidden" name="end_time" value="<?php echo htmlspecialchars($end_time); ?>">
                
                <label for="payment_slip" class="slip-upload-label">แนบสลิปการโอนเงิน</label>
                <input type="file" id="payment_slip" name="payment_slip" accept="image/*" required>
                
                <button type="submit" name="confirm_booking" class="confirm-button">ยืนยันการจองและแจ้งชำระเงิน</button>
            </form>
            
            <form action="payment.php" method="POST" id="credit_card-form" class="payment-form-section">
                <h2>ชำระเงินผ่านบัตรเครดิต</h2>
                <input type="hidden" name="payment_method" value="credit_card">
                <input type="hidden" name="booked_spot" value="<?php echo $selected_spot; ?>">
                <input type="hidden" name="start_time" value="<?php echo htmlspecialchars($start_time); ?>">
                <input type="hidden" name="end_time" value="<?php echo htmlspecialchars($end_time); ?>">

                <div class="credit-card-form">
                    <div class="form-group">
                        <label for="card_number">หมายเลขบัตรเครดิต</label>
                        <input type="text" id="card_number" name="card_number" pattern="[0-9]{16}" placeholder="xxxxxxxxxxxxxxxx" required>
                    </div>
                    <div class="form-group">
                        <label for="card_name">ชื่อบนบัตร</label>
                        <input type="text" id="card_name" name="card_name" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="expiry">วันหมดอายุ (MM/YY)</label>
                            <input type="text" id="expiry" name="expiry" pattern="(0[1-9]|1[0-2])\/[0-9]{2}" placeholder="MM/YY" required>
                        </div>
                        <div class="form-group">
                            <label for="cvv">รหัส CVV</label>
                            <input type="text" id="cvv" name="cvv" pattern="[0-9]{3,4}" required>
                        </div>
                    </div>
                </div>
                
                <button type="submit" name="confirm_booking" class="confirm-button">ยืนยันการชำระเงิน</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const methodButtons = document.querySelectorAll('.method-btn');
            const forms = document.querySelectorAll('.payment-form-section');

            methodButtons.forEach(button => {
                button.addEventListener('click', function() {
                    methodButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');

                    const selectedMethod = this.dataset.method;
                    forms.forEach(form => {
                        form.classList.remove('active');
                        if (form.id === `${selectedMethod}-form`) {
                            form.classList.add('active');
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>