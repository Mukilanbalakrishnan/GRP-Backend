<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include "../config.php";
$conn->set_charset("utf8mb4");

$data = json_decode(file_get_contents("php://input"), true);

$id          = $data['id'];
$title       = $data['title'];
$shortDesc   = $data['shortDesc'];
$aboutTitle  = $data['aboutTitle'];
$aboutIntro  = $data['aboutIntro'];
$features    = json_encode($data['features']);
$videoUrls   = json_encode($data['videoUrls']);
$thumbnail   = $data['thumbnail'] ?? null;

/* ============================
   IMAGE HANDLING (IMPORTANT)
   ============================ */
$imagePath = null;

// If NEW image (base64)
if ($thumbnail && preg_match('/^data:image\/(\w+);base64,/', $thumbnail, $type)) {
    $ext = strtolower($type[1]);
    $imageData = base64_decode(substr($thumbnail, strpos($thumbnail, ',') + 1));

    if (!is_dir("../../uploads/services")) {
        mkdir("../../uploads/services", 0777, true);
    }

    $fileName = uniqid("service_") . "." . $ext;
    file_put_contents("../../uploads/services/" . $fileName, $imageData);

    $imagePath = "uploads/services/" . $fileName;
}

// If image NOT changed → keep existing path
if (!$imagePath) {
    $stmt = $conn->prepare("SELECT thumbnail FROM main_services WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($existingImage);
    $stmt->fetch();
    $stmt->close();

    $imagePath = $existingImage;
}

/* ============================
   UPDATE QUERY
   ============================ */
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
    $imagePath,
    $id
);

$stmt->execute();

echo json_encode([
    "status" => true,
    "message" => "Service updated successfully"
]);
