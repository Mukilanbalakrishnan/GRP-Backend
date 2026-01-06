<?php
// 🔴 CORS HEADERS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// 🔴 PREFLIGHT
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

ini_set('display_errors', 1);
error_reporting(E_ALL);

// 🔴 DB CONNECTION
$conn = new mysqli("localhost", "root", "", "grp");
$conn->set_charset("utf8mb4");

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["status" => false, "message" => "Invalid JSON"]);
    exit;
}

// 🔹 FIELDS
$title       = $data['title'] ?? '';
$shortDesc   = $data['shortDesc'] ?? '';
$aboutTitle  = $data['aboutTitle'] ?? '';
$aboutIntro  = $data['aboutIntro'] ?? '';
$features    = json_encode($data['features'] ?? []);
$videoUrls   = json_encode($data['videoUrls'] ?? []);
$thumbnail64 = $data['thumbnail'] ?? '';

$thumbnailPath = null;

// 🔴 IMAGE HANDLING
if ($thumbnail64) {
    // Example: data:image/jpeg;base64,xxxx
    if (preg_match('/^data:image\/(\w+);base64,/', $thumbnail64, $type)) {
        $thumbnail64 = substr($thumbnail64, strpos($thumbnail64, ',') + 1);
        $extension = strtolower($type[1]); // jpg, png, webp

        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
            echo json_encode(["status" => false, "message" => "Invalid image type"]);
            exit;
        }

        $thumbnail64 = base64_decode($thumbnail64);

        if ($thumbnail64 === false) {
            echo json_encode(["status" => false, "message" => "Base64 decode failed"]);
            exit;
        }

        // 🔹 FILE NAME
        $fileName = uniqid("service_", true) . "." . $extension;
        $uploadDir = "../../uploads/main_services/";
        $filePath = $uploadDir . $fileName;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        file_put_contents($filePath, $thumbnail64);

        // 🔹 PATH STORED IN DB (RELATIVE)
        $thumbnailPath = "uploads/main_services/" . $fileName;
    }
}

// 🔴 INSERT QUERY
$stmt = $conn->prepare("
    INSERT INTO main_services
    (title, short_desc, about_title, about_intro, features, video_urls, thumbnail)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "sssssss",
    $title,
    $shortDesc,
    $aboutTitle,
    $aboutIntro,
    $features,
    $videoUrls,
    $thumbnailPath
);

$stmt->execute();

echo json_encode([
    "status" => true,
    "message" => "Service created successfully",
    "thumbnail" => $thumbnailPath
]);
