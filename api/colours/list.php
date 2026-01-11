<?php


ini_set('display_errors', 0); // ❌ hide HTML errors
error_reporting(E_ALL);

header('Content-Type: application/json');

include "../config.php";

$brandId = $_GET['brand_id'];

$res = $conn->query("
    SELECT * FROM brand_colours WHERE brand_id=$brandId
");

$data = [];
while ($r = $res->fetch_assoc()) $data[] = $r;

echo json_encode(["status" => true, "data" => $data]);
