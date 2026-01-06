<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

$conn = new mysqli("localhost", "root", "", "grp");

$id   = $_POST['id'] ?? 0;
$name = $_POST['name'] ?? '';
$role = $_POST['role'] ?? '';
$text = $_POST['text'] ?? '';

if (!$id || !$name || !$text) {
    echo json_encode(["status"=>false,"message"=>"Missing fields"]);
    exit;
}

// Get old image
$res = $conn->query("SELECT image FROM about_reviews WHERE id=$id");
$old = $res->fetch_assoc();
$imagePath = $old['image'] ?? "";

// If new image uploaded → delete old one
if (!empty($_FILES['image']['name'])) {

    if ($imagePath) {
        $oldFile = __DIR__ . "/../../" . $imagePath;
        if (file_exists($oldFile)) unlink($oldFile);
    }

    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $filename = "about_" . time() . "." . $ext;
    $target = "../../uploads/about/" . $filename;

    move_uploaded_file($_FILES['image']['tmp_name'], $target);
    $imagePath = "uploads/about/" . $filename;
}

// Update record
$stmt = $conn->prepare("
    UPDATE about_reviews
    SET name=?, role=?, text=?, image=?
    WHERE id=?
");
$stmt->bind_param("ssssi", $name, $role, $text, $imagePath, $id);
$stmt->execute();

echo json_encode(["status"=>true]);
