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
$productId = $data['id'] ?? null;

if (!$productId) {
    echo json_encode(["status"=>false,"message"=>"Missing product id"]);
    exit;
}

/* 1️⃣ FETCH & DELETE COLOUR IMAGES */
$colourRes = $conn->query("
    SELECT colour_image FROM brand_colours 
    WHERE brand_id IN (
        SELECT id FROM product_brands WHERE product_id = $productId
    )
");
while ($c = $colourRes->fetch_assoc()) {
    if ($c['colour_image'] && file_exists("../../".$c['colour_image'])) {
        unlink("../../".$c['colour_image']);
    }
}

/* 2️⃣ DELETE COLOURS */
$conn->query("
    DELETE FROM brand_colours 
    WHERE brand_id IN (
        SELECT id FROM product_brands WHERE product_id = $productId
    )
");

/* 3️⃣ FETCH & DELETE BRAND IMAGES */
$brandRes = $conn->query("
    SELECT main_image FROM product_brands WHERE product_id = $productId
");
while ($b = $brandRes->fetch_assoc()) {
    if ($b['main_image'] && file_exists("../../".$b['main_image'])) {
        unlink("../../".$b['main_image']);
    }
}

/* 4️⃣ DELETE BRANDS */
$conn->query("DELETE FROM product_brands WHERE product_id = $productId");

/* 5️⃣ FETCH & DELETE PRODUCT IMAGE */
$pRes = $conn->query("SELECT product_thumbnail FROM products WHERE id = $productId");
if ($p = $pRes->fetch_assoc()) {
    if ($p['product_thumbnail'] && file_exists("../../".$p['product_thumbnail'])) {
        unlink("../../".$p['product_thumbnail']);
    }
}

/* 6️⃣ DELETE PRODUCT */
$conn->query("DELETE FROM products WHERE id = $productId");

echo json_encode(["status"=>true]);
exit;
