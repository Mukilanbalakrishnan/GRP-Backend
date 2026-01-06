<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}



$conn = new mysqli("localhost","root","","grp");
$res = $conn->query("SELECT * FROM text_testimonials ORDER BY id DESC");

$data = [];
while($row = $res->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
