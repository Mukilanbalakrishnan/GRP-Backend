<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include "../config.php";

$today = date("Y-m-d");

// Check if today already exists
$stmt = $conn->prepare("SELECT id, visit_count FROM visits WHERE visit_date = ?");
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update count
    $row = $result->fetch_assoc();
    $newCount = $row['visit_count'] + 1;

    $update = $conn->prepare(
        "UPDATE visits SET visit_count = ? WHERE visit_date = ?"
    );
    $update->bind_param("is", $newCount, $today);
    $update->execute();
} else {
    // Insert first visit of day
    $insert = $conn->prepare(
        "INSERT INTO visits (visit_date, visit_count) VALUES (?, 1)"
    );
    $insert->bind_param("s", $today);
    $insert->execute();
}

echo json_encode(["status" => true]);
