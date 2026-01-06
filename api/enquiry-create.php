<?php
// enquiry-create.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ✅ Always return JSON
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");

// ✅ Disable HTML errors
ini_set('display_errors', 0);
error_reporting(0);

// ✅ Read JSON input
$input = json_decode(file_get_contents("php://input"), true);

if (!$input) {
    echo json_encode([
        "status" => false,
        "message" => "Invalid JSON input"
    ]);
    exit;
}

$name = trim($input['name'] ?? '');
$email = trim($input['email'] ?? '');
$phone = trim($input['phone'] ?? '');
$message = trim($input['message'] ?? '');

if (!$name || !$email || !$phone || !$message) {
    echo json_encode([
        "status" => false,
        "message" => "All fields required"
    ]);
    exit;
}

// ✅ Database connection
$conn = new mysqli("localhost", "root", "", "grp");

if ($conn->connect_error) {
    echo json_encode([
        "status" => false,
        "message" => "Database connection failed"
    ]);
    exit;
}

// ✅ Insert enquiry
$stmt = $conn->prepare(
    "INSERT INTO enquiries (name, email, phone, message) VALUES (?, ?, ?, ?)"
);
$stmt->bind_param("ssss", $name, $email, $phone, $message);

if ($stmt->execute()) {
    echo json_encode([
        "status" => true,
        "message" => "Enquiry saved"
    ]);
} else {
    echo json_encode([
        "status" => false,
        "message" => "Insert failed"
    ]);
}

$stmt->close();
$conn->close();
