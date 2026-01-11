<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include "../config.php";

$name = $_POST['name'] ?? '';
$role = $_POST['role'] ?? '';
$text = $_POST['text'] ?? '';

if (!$name || !$text) {
    echo json_encode(["status"=>false,"message"=>"Missing fields"]);
    exit;
}

$imagePath = "";

// 🔹 IMAGE UPLOAD
if (!empty($_FILES['image']['name'])) {
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $filename = "about_" . time() . "." . $ext;
    $target = "../../uploads/about/" . $filename;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        echo json_encode(["status"=>false,"message"=>"Image upload failed"]);
        exit;
    }

    $imagePath = "uploads/about/" . $filename;
}

$stmt = $conn->prepare("
    INSERT INTO about_reviews (name, role, text, image)
    VALUES (?,?,?,?)
");
$stmt->bind_param("ssss", $name, $role, $text, $imagePath);
$stmt->execute();

echo json_encode(["status"=>true]);
