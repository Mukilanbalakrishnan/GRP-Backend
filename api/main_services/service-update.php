<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "grp");
$conn->set_charset("utf8mb4");

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'];
$title = $data['title'];
$shortDesc = $data['shortDesc'];
$aboutTitle = $data['aboutTitle'];
$aboutIntro = $data['aboutIntro'];
$features = json_encode($data['features']);
$videoUrls = json_encode($data['videoUrls']);
$thumbnail = $data['thumbnail'];

$stmt = $conn->prepare("
    UPDATE main_services SET
        title = ?,
        short_desc = ?,
        about_title = ?,
        about_intro = ?,
        features = ?,
        video_urls = ?,
        thumbnail = ?
    WHERE id = ?
");

$stmt->bind_param(
    "sssssssi",
    $title,
    $shortDesc,
    $aboutTitle,
    $aboutIntro,
    $features,
    $videoUrls,
    $thumbnail,
    $id
);

$stmt->execute();

echo json_encode([
    "status" => true,
    "message" => "Service updated successfully"
]);
