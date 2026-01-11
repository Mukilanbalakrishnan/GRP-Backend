<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

include "../config.php";

$res = $conn->query("SELECT * FROM about_reviews ORDER BY id DESC");
$data = [];

while($row = $res->fetch_assoc()){
    $data[] = $row;
}

echo json_encode($data);
