<?php
ini_set('display_errors', 0);
error_reporting(0);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");

$conn = new mysqli("localhost", "root", "", "grp");
if ($conn->connect_error) {
    echo json_encode(["status" => false, "message" => "DB error"]);
    exit;
}

if (!isset($_FILES['image']) || !isset($_POST['position'])) {
    echo json_encode(["status" => false, "message" => "Invalid data"]);
    exit;
}

$position = (int) $_POST['position'];
$uploadDir = __DIR__ . "/../../uploads/hero/";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
$filename = "hero_{$position}_" . time() . "." . $ext;
$filepath = $uploadDir . $filename;

if (!move_uploaded_file($_FILES['image']['tmp_name'], $filepath)) {
    echo json_encode(["status" => false, "message" => "Upload failed"]);
    exit;
}

$imagePath = "uploads/hero/" . $filename;

$conn->query("DELETE FROM hero_images WHERE position = $position");

$stmt = $conn->prepare(
    "INSERT INTO hero_images (image_path, position) VALUES (?, ?)"
);
$stmt->bind_param("si", $imagePath, $position);
$stmt->execute();

echo json_encode([
    "status" => true,
    "path" => $imagePath
]);
