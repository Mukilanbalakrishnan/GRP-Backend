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



$conn = new mysqli("localhost","root","","grp");

$stmt = $conn->prepare("DELETE FROM text_testimonials WHERE id=?");
$stmt->bind_param("i", $_POST['id']);
$stmt->execute();

echo json_encode(["status"=>true]);
