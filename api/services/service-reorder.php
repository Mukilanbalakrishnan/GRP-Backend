<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

include "../config.php";
$data = json_decode(file_get_contents("php://input"), true);

foreach ($data as $pos => $id) {
    $stmt = $conn->prepare(
        "UPDATE services SET position=? WHERE id=?"
    );
    $stmt->bind_param("ii", $pos, $id);
    $stmt->execute();
}

$conn->close();
echo json_encode(["status" => true]);
