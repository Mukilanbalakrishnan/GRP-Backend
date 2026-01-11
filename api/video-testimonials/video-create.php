<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

include "../config.php";

// ✅ REQUIRED FIELDS CHECK
$required = ["name", "role", "title", "video_url", "thumbnail", "rating", "quote", "duration"];
foreach ($required as $field) {
    if (!isset($_POST[$field]) || $_POST[$field] === "") {
        echo json_encode(["status" => false, "message" => "Missing field: $field"]);
        exit;
    }
}

$stmt = $conn->prepare("
    INSERT INTO video_testimonials
    (name, role, title, video_url, thumbnail, rating, quote, duration)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "sssssiss",
    $_POST['name'],
    $_POST['role'],
    $_POST['title'],
    $_POST['video_url'],
    $_POST['thumbnail'],
    $_POST['rating'],
    $_POST['quote'],
    $_POST['duration']
);

if (!$stmt->execute()) {
    echo json_encode(["status" => false, "message" => $stmt->error]);
    exit;
}

echo json_encode(["status" => true, "message" => "Video testimonial added"]);
