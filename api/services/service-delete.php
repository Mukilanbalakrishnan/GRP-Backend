<?php
ini_set('display_errors', 0);
error_reporting(0);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");

include "../config.php";

$id = $_POST['id'] ?? null;
if (!$id) {
    echo json_encode(["status" => false]);
    exit;
}

/* Get image */
$stmt = $conn->prepare("SELECT image_path FROM services WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

/* Delete file */
$absPath = __DIR__ . "/../../" . $row['image_path'];
$absPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $absPath);

if (file_exists($absPath)) unlink($absPath);

/* Delete DB row */
$del = $conn->prepare("DELETE FROM services WHERE id=?");
$del->bind_param("i", $id);
$del->execute();

$conn->close();
echo json_encode(["status" => true]);
