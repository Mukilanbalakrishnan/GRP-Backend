<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}



include "../config.php";

$stmt = $conn->prepare("
UPDATE text_testimonials SET
name=?, role=?, text=?, rating=?
WHERE id=?
");

$stmt->bind_param(
    "sssii",
    $_POST['name'],
    $_POST['role'],
    $_POST['text'],
    $_POST['rating'],
    $_POST['id']
);

$stmt->execute();
echo json_encode(["status"=>true]);
