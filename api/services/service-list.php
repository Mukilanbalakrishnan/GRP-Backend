<?php
ini_set('display_errors', 0);
error_reporting(0);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");

include "../config.php";

$res = $conn->query(
    "SELECT * FROM services WHERE status=1 ORDER BY position ASC, id DESC"
);

$data = [];
while ($row = $res->fetch_assoc()) {
    $data[] = $row;
}

$conn->close();
echo json_encode($data);
