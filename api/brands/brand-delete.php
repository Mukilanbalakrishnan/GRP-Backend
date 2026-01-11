<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

include "../config.php";

$id = $_POST['id'] ?? null;
if (!$id) {
    echo json_encode(["status" => false]);
    exit;
}

$stmt = $conn->prepare("SELECT image_path FROM brands WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

$absPath = realpath(__DIR__ . "/../../") . DIRECTORY_SEPARATOR . $row['image_path'];
$absPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $absPath);

if (file_exists($absPath)) unlink($absPath);

$del = $conn->prepare("DELETE FROM brands WHERE id=?");
$del->bind_param("i", $id);
$del->execute();

$conn->close();
echo json_encode(["status" => true]);
