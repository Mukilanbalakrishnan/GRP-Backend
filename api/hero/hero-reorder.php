<?php
ini_set('display_errors', 0);
error_reporting(0);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");
$conn = new mysqli("localhost", "root", "", "grp");

$data = json_decode(file_get_contents("php://input"), true);

foreach ($data as $img) {
    $conn->query(
        "UPDATE hero_images 
         SET position={$img['position']} 
         WHERE id={$img['id']}"
    );
}

echo json_encode(["status" => true]);
