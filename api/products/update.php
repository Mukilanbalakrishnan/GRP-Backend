<?php
require_once __DIR__ . "/../config.php";


header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");


$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'] ?? null;
$title = $data['title'] ?? '';
$description = $data['description'] ?? '';
$thumbnail = $data['thumbnail'] ?? null;

if (!$id) {
    echo json_encode(["status" => false, "message" => "ID missing"]);
    exit;
}

/* =========================
   UPDATE TEXT FIELDS
========================= */
mysqli_query($conn, "
  UPDATE products 
  SET product_name='$title', short_description='$description'
  WHERE id='$id'
");

/* =========================
   HANDLE IMAGE (ONLY IF SENT)
========================= */
if ($thumbnail && strpos($thumbnail, 'data:image') === 0) {

    // Decode base64
    $thumbnail = explode(',', $thumbnail)[1];
    $imageData = base64_decode($thumbnail);

    // Create filename
    $fileName = "uploads/products/section_" . time() . ".jpg";
    $filePath = "../../" . $fileName;

    // Save file
    file_put_contents($filePath, $imageData);

    // Update DB with new image path
    mysqli_query($conn, "
      UPDATE products 
      SET product_thumbnail='$fileName'
      WHERE id='$id'
    ");
}

echo json_encode(["status" => true]);
