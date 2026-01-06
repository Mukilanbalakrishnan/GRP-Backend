<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

$conn = new mysqli("localhost", "root", "", "grp");

$id = $_POST['id'] ?? 0;
if (!$id) {
    echo json_encode(["status"=>false,"message"=>"Invalid ID"]);
    exit;
}

$res = $conn->query("SELECT image FROM about_reviews WHERE id=$id");
$row = $res->fetch_assoc();

if ($row && $row['image']) {
    $filePath = __DIR__ . "/../../" . $row['image'];

    if (file_exists($filePath)) {
        unlink($filePath); // ✅ ACTUALLY deletes
    }
}

$conn->query("DELETE FROM about_reviews WHERE id=$id");

echo json_encode(["status"=>true]);
