<?php
ini_set('display_errors', 0);
error_reporting(0);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");

include "../config.php";

$sql = "SELECT id, title, short_desc, thumbnail FROM main_services ORDER BY id DESC";
$result = $conn->query($sql);

if (!$result) {
    echo json_encode([
        "status" => false,
        "message" => "Query failed"
    ]);
    exit;
}

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode([
    "status" => true,
    "data" => $data
]);
