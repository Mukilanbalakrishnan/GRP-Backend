<?php



header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");
include "../config.php";

$data = json_decode(file_get_contents("php://input"), true);

$title = $data['title'] ?? '';
$desc  = $data['description'] ?? '';
$image = $data['thumbnail'] ?? '';

$imagePath = null;

if ($image && preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {
    $ext = strtolower($type[1]);
    $image = base64_decode(substr($image, strpos($image, ',') + 1));
    $name = uniqid("product_") . "." . $ext;

    if (!is_dir("../../uploads/products")) mkdir("../../uploads/products", 0777, true);
    file_put_contents("../../uploads/products/$name", $image);

    $imagePath = "uploads/products/$name";
}

$stmt = $conn->prepare("
    INSERT INTO products (product_name, short_description, product_thumbnail)
    VALUES (?, ?, ?)
");
$stmt->bind_param("sss", $title, $desc, $imagePath);
$stmt->execute();

echo json_encode(["status" => true]);
