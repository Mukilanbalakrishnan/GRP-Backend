<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include "../config.php";
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    echo json_encode(["status" => false, "message" => "DB error"]);
    exit;
}

$id = $_GET['id'] ?? null;

/* ---------------------------
   SINGLE SERVICE (DETAIL)
---------------------------- */
if ($id) {
    $stmt = $conn->prepare("
        SELECT id, title, short_desc, thumbnail,
               about_title, about_intro,
               features, video_urls
        FROM main_services
        WHERE id = ?
        LIMIT 1
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        echo json_encode([
            "status" => true,
            "data" => [
                "id" => $row["id"],
                "title" => $row["title"],
                "shortDesc" => $row["short_desc"],
                "thumbnail" => $row["thumbnail"],
                "aboutTitle" => $row["about_title"],
                "aboutDescription" => $row["about_intro"],
                "features" => json_decode($row["features"], true) ?? [],
                "videos" => json_decode($row["video_urls"], true) ?? []
            ]
        ]);
    } else {
        echo json_encode(["status" => false, "message" => "Service not found"]);
    }
    exit;
}

/* ---------------------------
   ALL SERVICES (LIST)
---------------------------- */
$result = $conn->query("
    SELECT id, title, short_desc, thumbnail
    FROM main_services
    ORDER BY id DESC
");

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode([
    "status" => true,
    "data" => $data
]);
