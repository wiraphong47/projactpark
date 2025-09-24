<?php
require_once 'config.php';

header('Content-Type: application/json');

$spot_type = $_POST['spot_type'] ?? 'all';
$start_time = $_POST['start_time'] ?? null;
$end_time = $_POST['end_time'] ?? null;

$available_spots = [];

if ($start_time && $end_time) {
    // คำสั่ง SQL เพื่อหาที่จอดที่ว่างในช่วงเวลาที่ระบุ
    $sql = "
        SELECT ps.spot_name, ps.spot_type
        FROM parking_spots ps
        LEFT JOIN bookings b ON ps.spot_name = b.spot_name
        WHERE ps.spot_name LIKE 'A%' AND b.booking_id IS NULL 
        OR NOT (
            (b.booked_from BETWEEN ? AND ?) OR
            (b.booked_to BETWEEN ? AND ?) OR
            (? BETWEEN b.booked_from AND b.booked_to) OR
            (? BETWEEN b.booked_from AND b.booked_to)
        )
    ";

    // เพิ่มเงื่อนไข spot_type
    if ($spot_type !== 'all') {
        $sql .= " AND ps.spot_type = ?";
    }

    $stmt = $conn->prepare($sql);
    
    // Bind parameters
    if ($spot_type !== 'all') {
        $stmt->bind_param("ssssss", $start_time, $end_time, $start_time, $end_time, $start_time, $end_time, $spot_type);
    } else {
        $stmt->bind_param("ssss", $start_time, $end_time, $start_time, $end_time);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $available_spots[] = $row;
    }

    $stmt->close();
}

$conn->close();

echo json_encode($available_spots);
?>