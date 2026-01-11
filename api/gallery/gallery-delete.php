<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include "../config.php";

$ids = $_POST['ids'] ?? [];

if (!is_array($ids) || empty($ids)) {
    echo json_encode(["status" => false]);
    exit;
}

$idList = implode(",", array_map('intval', $ids));
$conn->query("DELETE FROM gallery WHERE id IN ($idList)");

echo json_encode(["status" => true]);
