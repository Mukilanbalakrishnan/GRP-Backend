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

$brandId = $data['brand_id'] ?? null;
$colourId = $data['id'] ?? null;

/* 🔥 DELETE ALL COLOURS OF A BRAND (EDIT MODE) */
if ($brandId && isset($data['delete_all'])) {

    $res = $conn->query("SELECT colour_image FROM brand_colours WHERE brand_id=$brandId");
    while ($c = $res->fetch_assoc()) {
        if ($c['colour_image'] && file_exists("../../".$c['colour_image'])) {
            unlink("../../".$c['colour_image']);
        }
    }

    $conn->query("DELETE FROM brand_colours WHERE brand_id=$brandId");
    echo json_encode(["status"=>true]);
    exit;
}

/* SINGLE COLOUR DELETE */
if ($colourId) {
    $res = $conn->query("SELECT colour_image FROM brand_colours WHERE id=$colourId");
    if ($c = $res->fetch_assoc()) {
        if ($c['colour_image'] && file_exists("../../".$c['colour_image'])) {
            unlink("../../".$c['colour_image']);
        }
    }

    $conn->query("DELETE FROM brand_colours WHERE id=$colourId");
    echo json_encode(["status"=>true]);
    exit;
}

echo json_encode(["status"=>false, "message"=>"Invalid request"]);
