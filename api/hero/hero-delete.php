<?php
ini_set('display_errors', 0);
error_reporting(0);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");

$conn = new mysqli("localhost", "root", "", "grp");
if ($conn->connect_error) {
    echo json_encode(["status" => false, "message" => "DB connection failed"]);
    exit;
}

$id = $_POST['id'] ?? null;
if (!$id) {
    echo json_encode(["status" => false, "message" => "Invalid ID"]);
    exit;
}

// 1️⃣ Get image path from DB
$stmt = $conn->prepare("SELECT image_path FROM hero_images WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => false, "message" => "Image not found"]);
    exit;
}

$row = $result->fetch_assoc();

// ✅ Build absolute path safely
$absolutePath = realpath(__DIR__ . "/../../" . $row['image_path']);

// DEBUG (temporary)
// echo json_encode(["path" => $absolutePath]); exit;

// 2️⃣ Delete file from uploads
if ($absolutePath && file_exists($absolutePath)) {
    unlink($absolutePath);
}


// 3️⃣ Delete DB row
$del = $conn->prepare("DELETE FROM hero_images WHERE id = ?");
$del->bind_param("i", $id);
$del->execute();

echo json_encode(["status" => true, "message" => "Image deleted"]);

$conn->close();
