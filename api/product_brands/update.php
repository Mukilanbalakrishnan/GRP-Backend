<?php
// ===============================
// CORS — ONLY FOR THIS FILE
// ===============================
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ===============================
// DB
// ===============================
require_once __DIR__ . "/../config.php";

// ===============================
// INPUT
// ===============================
$data = json_decode(file_get_contents("php://input"), true);

$id      = $data['id'] ?? null;
$name    = $data['name'] ?? '';
$image64 = $data['image'] ?? '';

if (!$id || !$name) {
    echo json_encode([
        "status" => false,
        "message" => "Missing brand id or name"
    ]);
    exit;
}

$imagePath = null;

// ===============================
// IMAGE UPDATE (OPTIONAL)
// ===============================
if ($image64 && preg_match('/^data:image\/(\w+);base64,/', $image64, $t)) {

    $ext = strtolower($t[1]);
    $img = base64_decode(substr($image64, strpos($image64, ',') + 1));

    if (!in_array($ext, ['jpg','jpeg','png','webp'])) {
        echo json_encode([
            "status" => false,
            "message" => "Invalid image type"
        ]);
        exit;
    }

    if (!is_dir("../../uploads/product_brands")) {
        mkdir("../../uploads/product_brands", 0777, true);
    }

    $file = uniqid("brand_") . "." . $ext;
    file_put_contents("../../uploads/product_brands/$file", $img);

    $imagePath = "uploads/product_brands/$file";

    $stmt = $conn->prepare("
        UPDATE product_brands 
        SET brand_name = ?, main_image = ?
        WHERE id = ?
    ");
    $stmt->bind_param("ssi", $name, $imagePath, $id);

} else {
    // Only name update
    $stmt = $conn->prepare("
        UPDATE product_brands 
        SET brand_name = ?
        WHERE id = ?
    ");
    $stmt->bind_param("si", $name, $id);
}

// ===============================
// EXECUTE
// ===============================
if ($stmt->execute()) {
    echo json_encode(["status" => true]);
} else {
    echo json_encode([
        "status" => false,
        "message" => "Update failed",
        "error" => $stmt->error
    ]);
}
exit;
