<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

ini_set('display_errors', 0);
error_reporting(0);

require_once "../config.php";


$productId = intval($_GET['product_id'] ?? 0);

if (!$productId) {
  echo json_encode(["status" => false, "message" => "Invalid product id"]);
  exit;
}

// 1️⃣ Get brands
$brandsRes = $conn->query("
  SELECT id, brand_name, main_image
  FROM product_brands
  WHERE product_id = $productId
");

$brands = [];

while ($brand = $brandsRes->fetch_assoc()) {
  $brandId = $brand['id'];

  // 2️⃣ Get colours for each brand
  $colRes = $conn->query("
    SELECT colour_name, colour_image
    FROM brand_colours
    WHERE brand_id = $brandId
  ");

  $colours = [];
  while ($c = $colRes->fetch_assoc()) {
    $colours[] = $c;
  }

  // 3️⃣ Attach colours
  $brand['colours'] = $colours;
  $brands[] = $brand;
}

echo json_encode([
  "status" => true,
  "data" => $brands
]);
