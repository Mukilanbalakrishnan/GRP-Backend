<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    include "../config.php";
    $conn->set_charset("utf8mb4");

    $title    = $_POST['title'] ?? '';
    $category = $_POST['category'] ?? '';

    if (!$title || !$category || empty($_FILES['image'])) {
        throw new Exception("Missing required fields");
    }

    $uploadDir = __DIR__ . "/../../uploads/gallery/";
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $fileName = uniqid("gallery_") . "." . $ext;
    move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $fileName);

    $imagePath = "uploads/gallery/" . $fileName;

    $stmt = $conn->prepare("
        INSERT INTO gallery (title, category, image_path)
        VALUES (?, ?, ?)
    ");
    $stmt->bind_param("sss", $title, $category, $imagePath);
    $stmt->execute();

    echo json_encode([
        "status" => true,
        "id" => $stmt->insert_id
    ]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        "status" => false,
        "error" => $e->getMessage()
    ]);
}
