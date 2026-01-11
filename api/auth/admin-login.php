<?php
// =======================
// CORS HEADERS (REQUIRED)
// =======================
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Handle preflight request
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
// FETCH ADMIN
// =======================
$stmt = $conn->prepare("SELECT id, password FROM admins WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(401);
    echo json_encode([
        "status" => false,
        "message" => "Invalid credentials"
    ]);
    exit;
}

$admin = $result->fetch_assoc();

// =======================
// VERIFY PASSWORD
// =======================
if (!password_verify($password, $admin['password'])) {
    http_response_code(401);
    echo json_encode([
        "status" => false,
        "message" => "Invalid credentials"
    ]);
    exit;
}

// =======================
// LOGIN SUCCESS
// =======================
echo json_encode([
    "status" => true,
    "token" => "grp-admin-token-" . time()
]);
