<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include "../config.php";


$res = $conn->query("
    SELECT id, title, category, image_path, created_at
    FROM gallery
    ORDER BY id DESC
");

$data = [];
while ($row = $res->fetch_assoc()) {
    $data[] = [
        "id" => $row['id'],
        "title" => $row['title'],
        "category" => $row['category'],
        "url" => "http://localhost/GRP-Backend/" . $row['image_path'],
        "date" => $row['created_at']
    ];
}



echo json_encode($data);
