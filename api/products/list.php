<?php
// ===============================
// CORS — ONLY FOR THIS FILE
// ===============================
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight safely
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ===============================
// DB
// ===============================
require_once __DIR__ . "/../config.php";

$data = [];

/* PRODUCTS */
$products = $conn->query("
    SELECT id, product_name, short_description, product_thumbnail
    FROM products
");

if (!$products) {
    echo json_encode([
        "status" => false,
        "message" => "Product query failed",
        "error" => $conn->error
    ]);
    exit;
}

while ($p = $products->fetch_assoc()) {

    $brands = [];

    /* BRANDS */
    $brandStmt = $conn->prepare("
        SELECT id, brand_name, main_image
        FROM product_brands
        WHERE product_id = ?
    ");
    $brandStmt->bind_param("i", $p['id']);
    $brandStmt->execute();
    $brandRes = $brandStmt->get_result();

    while ($b = $brandRes->fetch_assoc()) {

        $colours = [];

        /* COLOURS */
        $colourStmt = $conn->prepare("
            SELECT id, colour_name, colour_image
            FROM brand_colours
            WHERE brand_id = ?
        ");
        $colourStmt->bind_param("i", $b['id']);
        $colourStmt->execute();
        $colourRes = $colourStmt->get_result();

        while ($c = $colourRes->fetch_assoc()) {
            $colours[] = $c;
        }

        $brands[] = [
            "id" => $b['id'],
            "brand_name" => $b['brand_name'],
            "main_image" => $b['main_image'],
            "colours" => $colours
        ];
    }

    $data[] = [
        "id" => $p['id'],
        "product_name" => $p['product_name'],
        "short_description" => $p['short_description'],
        "product_thumbnail" => $p['product_thumbnail'],
        "tiles" => $brands
    ];
}

echo json_encode([
    "status" => true,
    "data" => $data
]);
exit;
