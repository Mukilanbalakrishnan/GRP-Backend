<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "grp");
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    echo json_encode(["status" => false, "message" => "DB error"]);
    exit;
}

$result = $conn->query("
    SELECT 
        id,
        title,
        short_desc,
        thumbnail,
        about_title,
        about_intro,
        features,
        video_urls
    FROM main_services
    ORDER BY id DESC
");

$services = [];

while ($row = $result->fetch_assoc()) {
    $services[] = [
        "id" => $row["id"],
        "title" => $row["title"],
        "short_desc" => $row["short_desc"],
        "thumbnail" => $row["thumbnail"],
        "about_title" => $row["about_title"],
        "about_intro" => $row["about_intro"],
        "features" => json_decode($row["features"], true) ?? [],
        "video_urls" => json_decode($row["video_urls"], true) ?? []
    ];
}

echo json_encode([
    "status" => true,
    "data" => $services
]);
