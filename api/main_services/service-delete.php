<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include "../config.php";
$conn->set_charset("utf8mb4");

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'];

$stmt = $conn->prepare("DELETE FROM main_services WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

echo json_encode([
    "status" => true,
    "message" => "Service deleted successfully"
]);
