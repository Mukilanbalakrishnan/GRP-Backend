<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

$conn = new mysqli("localhost","root","","grp");

if ($conn->connect_error) {
    echo json_encode(["status" => false, "message" => "DB connection failed"]);
    exit;
}

$stmt = $conn->prepare("
UPDATE video_testimonials SET
name=?, role=?, title=?, video_url=?, thumbnail=?, rating=?, quote=?, duration=?
WHERE id=?
");

$stmt->bind_param(
  "sssssissi",
  $_POST['name'],
  $_POST['role'],
  $_POST['title'],
  $_POST['video_url'],
  $_POST['thumbnail'],
  $_POST['rating'],
  $_POST['quote'],
  $_POST['duration'],
  $_POST['id']
);

if (!$stmt->execute()) {
    echo json_encode(["status"=>false, "message"=>$stmt->error]);
    exit;
}

echo json_encode(["status"=>true, "message"=>"Video testimonial updated"]);
