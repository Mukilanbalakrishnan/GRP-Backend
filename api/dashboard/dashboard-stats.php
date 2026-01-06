<?php
// =======================
// CORS
// =======================
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/json");

// =======================
// DB
// =======================
$conn = new mysqli("localhost", "root", "", "grp");
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    echo json_encode(["status" => false]);
    exit;
}

// =======================
// COUNTS
// =======================

// 1️⃣ Enquiries count
$enquiries = $conn->query("SELECT COUNT(*) as total FROM enquiries")
                  ->fetch_assoc()['total'];

// 2️⃣ Services count
$services = $conn->query("SELECT COUNT(*) as total FROM main_services")
                 ->fetch_assoc()['total'];

// 3️⃣ Gallery count
$gallery = $conn->query("SELECT COUNT(*) as total FROM gallery")
                ->fetch_assoc()['total'];

// 4️⃣ Visits count (example table: visits)
$visits = $conn->query(
    "SELECT SUM(visit_count) as total FROM visits"
)->fetch_assoc()['total'] ?? 0;


// =======================
// RESPONSE
// =======================
echo json_encode([
    "status" => true,
    "data" => [
        "visits" => (int)$visits,
        "enquiries" => (int)$enquiries,
        "services" => (int)$services,
        "gallery" => (int)$gallery
    ]
]);
