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
    $conn = new mysqli("localhost", "root", "", "grp");
    $conn->set_charset("utf8mb4");

    $id       = $_POST['id'] ?? '';
    $title    = $_POST['title'] ?? '';
    $category = $_POST['category'] ?? '';

    if (!$id || !$title || !$category) {
        throw new Exception("Missing fields");
    }

    $imageSql = "";
    if (!empty($_FILES['image']['name'])) {
        $dir = __DIR__ . "/../../uploads/gallery/";
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $file = uniqid("gallery_") . "." . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], $dir . $file);

        $imageSql = ", image_path='uploads/gallery/$file'";
    }

    $conn->query("
        UPDATE gallery 
        SET title='$title', category='$category' $imageSql
        WHERE id=$id
    ");

    echo json_encode(["status" => true]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        "status" => false,
        "error" => $e->getMessage()
    ]);
}
