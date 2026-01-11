<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . "/../config.php";

$data = json_decode(file_get_contents("php://input"), true);
$brandId = $data['id'] ?? null;

if (!$brandId) {
    echo json_encode(["status"=>false,"message"=>"Missing brand id"]);
    exit;
}

/* 1️⃣ DELETE COLOUR IMAGES */
$colourRes = $conn->query("SELECT colour_image FROM brand_colours WHERE brand_id=$brandId");
while ($c = $colourRes->fetch_assoc()) {
    if ($c['colour_image'] && file_exists("../../".$c['colour_image'])) {
        unlink("../../".$c['colour_image']);
    }
}

/* 2️⃣ DELETE COLOURS */
$conn->query("DELETE FROM brand_colours WHERE brand_id=$brandId");

/* 3️⃣ DELETE BRAND IMAGE */
$bRes = $conn->query("SELECT main_image FROM product_brands WHERE id=$brandId");
if ($b = $bRes->fetch_assoc()) {
    if ($b['main_image'] && file_exists("../../".$b['main_image'])) {
        unlink("../../".$b['main_image']);
    }
}

/* 4️⃣ DELETE BRAND */
$conn->query("DELETE FROM product_brands WHERE id=$brandId");

echo json_encode(["status"=>true]);
exit;
