<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

include "../config.php";


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
