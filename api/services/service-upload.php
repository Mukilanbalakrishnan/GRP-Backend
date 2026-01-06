<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

$conn = new mysqli("localhost", "root", "", "grp");
if ($conn->connect_error) {
    echo json_encode(["status" => false, "message" => "DB error"]);
    exit;
}

$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';

if (!$title || !$description || !isset($_FILES['image'])) {
    echo json_encode(["status" => false, "message" => "Missing fields"]);
    exit;
}

/* Upload directory (NO realpath) */
$uploadDir = __DIR__ . "/../../uploads/services/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
$fileName = uniqid("service_") . "." . $ext;
$targetPath = $uploadDir . $fileName;

if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
    echo json_encode(["status" => false, "message" => "Upload failed"]);
    exit;
}

$imagePathDB = "uploads/services/" . $fileName;

/* Position */
$posRes = $conn->query("SELECT MAX(position) AS maxpos FROM services");
$row = $posRes->fetch_assoc();
$position = ($row['maxpos'] ?? 0) + 1;

$stmt = $conn->prepare(
    "INSERT INTO services (title, description, image_path, position)
     VALUES (?, ?, ?, ?)"
);
$stmt->bind_param("sssi", $title, $description, $imagePathDB, $position);
$stmt->execute();

$stmt->close();
$conn->close();

echo json_encode(["status" => true]);
