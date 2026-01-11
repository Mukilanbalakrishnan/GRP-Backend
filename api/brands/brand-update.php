<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

include "../config.php";

$id = $_POST['id'] ?? null;
$name = $_POST['name'] ?? '';

if (!$id || !$name) {
    echo json_encode(["status" => false, "message" => "Invalid input"]);
    exit;
}

// Get old image
$stmt = $conn->prepare("SELECT image_path FROM brands WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

$imagePathDB = $row['image_path'];

if (isset($_FILES['image'])) {
    $absOld = realpath(__DIR__ . "/../../") . DIRECTORY_SEPARATOR . $imagePathDB;
    if (file_exists($absOld)) unlink($absOld);

    $dir = realpath(__DIR__ . "/../../uploads/brands") . DIRECTORY_SEPARATOR;
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $fileName = uniqid("brand_") . "." . $ext;
    move_uploaded_file($_FILES['image']['tmp_name'], $dir . $fileName);
    $imagePathDB = "uploads/brands/" . $fileName;
}

$upd = $conn->prepare("UPDATE brands SET name=?, image_path=? WHERE id=?");
$upd->bind_param("ssi", $name, $imagePathDB, $id);
$upd->execute();

$conn->close();
echo json_encode(["status" => true]);
