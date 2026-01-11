<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include "../config.php";

$data = json_decode(file_get_contents("php://input"), true);

$id    = $data['id'] ?? null;
$name  = $data['name'] ?? '';
$image = $data['image'] ?? '';

if (!$id || !$name) {
    echo json_encode(["status"=>false,"message"=>"Missing data"]);
    exit;
}

/* Fetch old image */
$res = $conn->query("SELECT colour_image FROM brand_colours WHERE id=$id");
$row = $res->fetch_assoc();
$imagePath = $row['colour_image'];

/* Replace image only if new base64 sent */
if ($image && preg_match('/^data:image\/(\w+);base64,/', $image, $t)) {

    if ($imagePath && file_exists("../../".$imagePath)) {
        unlink("../../".$imagePath);
    }

    $ext = strtolower($t[1]);
    $img = base64_decode(substr($image, strpos($image, ',') + 1));

    if (!is_dir("../../uploads/colours")) {
        mkdir("../../uploads/colours", 0777, true);
    }

    $file = uniqid("colour_", true).".".$ext;
    file_put_contents("../../uploads/colours/$file", $img);
    $imagePath = "uploads/colours/$file";
}

$stmt = $conn->prepare("
  UPDATE brand_colours 
  SET colour_name=?, colour_image=? 
  WHERE id=?
");
$stmt->bind_param("ssi", $name, $imagePath, $id);
$stmt->execute();

echo json_encode(["status"=>true]);
