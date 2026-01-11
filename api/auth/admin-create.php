<?php
// =======================
// CORS HEADERS (REQUIRED)
// =======================
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// =======================
// DATABASE
// =======================
include "../config.php";
$conn->set_charset("utf8mb4");



// =======================
// READ JSON INPUT
// =======================
$input = json_decode(file_get_contents("php://input"), true);

$email = trim($input['email'] ?? '');
$password = trim($input['password'] ?? '');

if (!$email || !$password) {
    http_response_code(400);
    echo json_encode([
        "status" => false,
        "message" => "Email and password required"
    ]);
    exit;
}

// =======================
// CHECK EXISTING ADMIN
// =======================
$check = $conn->prepare("SELECT id FROM admins WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    http_response_code(409);
    echo json_encode([
        "status" => false,
        "message" => "Admin already exists"
    ]);
    exit;
}

// =======================
// HASH PASSWORD (AUTO)
// =======================
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// =======================
// INSERT ADMIN
// =======================
$stmt = $conn->prepare(
    "INSERT INTO admins (email, password) VALUES (?, ?)"
);
$stmt->bind_param("ss", $email, $hashedPassword);
$stmt->execute();

// =======================
// SUCCESS RESPONSE
// =======================
echo json_encode([
    "status" => true,
    "message" => "Admin created successfully"
]);
