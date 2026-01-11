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

$required = ["name","text","rating"];
foreach($required as $f){
    if(empty($_POST[$f])){
        echo json_encode(["status"=>false,"message"=>"Missing $f"]);
        exit;
    }
}

$stmt = $conn->prepare("
INSERT INTO text_testimonials (name, role, text, rating)
VALUES (?,?,?,?)
");

$stmt->bind_param(
    "sssi",
    $_POST['name'],
    $_POST['role'],
    $_POST['text'],
    $_POST['rating']
);

$stmt->execute();
echo json_encode(["status"=>true]);
