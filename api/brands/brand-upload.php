<?php
ini_set('display_errors', 0);
error_reporting(0);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST"); 

include "../config.php";

$name = $_POST['name'] ?? '';
$gradient = $_POST['gradient'] ?? '';

if (!$name || !$gradient || !isset($_FILES['image'])) {
    echo json_encode(["status" => false, "message" => "Missing fields"]);
    exit;
}

$uploadDir = realpath(__DIR__ . "/../../uploads/brands") . DIRECTORY_SEPARATOR;

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
$fileName = uniqid("brand_") . "." . $ext;
$targetPath = $uploadDir . $fileName;

if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
    echo json_encode(["status" => false, "message" => "Upload failed"]);
    exit;
}

$imagePathDB = "uploads/brands/" . $fileName;

$posRes = $conn->query("SELECT MAX(position) AS maxpos FROM brands");
$posRow = $posRes->fetch_assoc();
$position = ($posRow['maxpos'] ?? 0) + 1;

$stmt = $conn->prepare(
    "INSERT INTO brands (name, image_path, gradient, position) VALUES (?, ?, ?, ?)"
);
$stmt->bind_param("sssi", $name, $imagePathDB, $gradient, $position);

$stmt->execute();
$stmt->close();
$conn->close();

echo json_encode(["status" => true, "message" => "Brand added"]);
