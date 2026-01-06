<?php
ini_set('display_errors', 0);
error_reporting(0);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");

$conn = new mysqli("localhost", "root", "", "grp");
if ($conn->connect_error) {
    echo json_encode(["status" => false]);
    exit;
}

$id = $_POST['id'] ?? null;
$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';

if (!$id || !$title || !$description) {
    echo json_encode(["status" => false, "message" => "Invalid input"]);
    exit;
}

/* Fetch old image */
$stmt = $conn->prepare("SELECT image_path FROM services WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

$imagePathDB = $row['image_path'];

/* Replace image if new one uploaded */
if (isset($_FILES['image'])) {
    $absOld = __DIR__ . "/../../" . $imagePathDB;
    $absOld = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $absOld);

    if (file_exists($absOld)) unlink($absOld);

    $dir = __DIR__ . "/../../uploads/services/";
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $fileName = uniqid("service_") . "." . $ext;

    move_uploaded_file($_FILES['image']['tmp_name'], $dir . $fileName);
    $imagePathDB = "uploads/services/" . $fileName;
}

$upd = $conn->prepare(
    "UPDATE services SET title=?, description=?, image_path=? WHERE id=?"
);
$upd->bind_param("sssi", $title, $description, $imagePathDB, $id);
$upd->execute();

$conn->close();
echo json_encode(["status" => true]);
