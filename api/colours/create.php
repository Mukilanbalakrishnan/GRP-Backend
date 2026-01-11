<?php
// ===============================
// CORS — MUST BE FIRST (NO OUTPUT)
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
// ERROR HANDLING
// ===============================
ini_set('display_errors', 0);
error_reporting(0);

// ===============================
// DB
// ===============================
require_once __DIR__ . "/../config.php";

// ===============================
// READ JSON
// ===============================
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode([
        "status" => false,
        "message" => "Invalid JSON"
    ]);
    exit;
}

$brandId = $data['brand_id'] ?? null;
$name    = $data['name'] ?? '';
$image64 = $data['image'] ?? '';

if (!$brandId || !$name) {
    echo json_encode([
        "status" => false,
        "message" => "Missing brand_id or colour name"
    ]);
    exit;
}

$imagePath = null;

// ===============================
// IMAGE SAVE
// ===============================
if ($image64 && preg_match('/^data:image\/(\w+);base64,/', $image64, $t)) {

    $ext = strtolower($t[1]);

    if (!in_array($ext, ['jpg','jpeg','png','webp'])) {
        echo json_encode([
            "status" => false,
            "message" => "Invalid image type"
        ]);
        exit;
    }

    $img = base64_decode(substr($image64, strpos($image64, ',') + 1));

    if (!is_dir("../../uploads/colours")) {
        mkdir("../../uploads/colours", 0777, true);
    }

    $file = uniqid("colour_", true) . "." . $ext;
    file_put_contents("../../uploads/colours/$file", $img);

    $imagePath = "uploads/colours/$file";
}

// ===============================
// INSERT
// ===============================
$stmt = $conn->prepare("
    INSERT INTO brand_colours (brand_id, colour_name, colour_image)
    VALUES (?, ?, ?)
");
$stmt->bind_param("iss", $brandId, $name, $imagePath);

if ($stmt->execute()) {
    echo json_encode([
        "status" => true,
        "id" => $stmt->insert_id
    ]);
} else {
    echo json_encode([
        "status" => false,
        "message" => "Insert failed",
        "error" => $stmt->error
    ]);
}
exit;
