<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include "config.php";

$sql = "SELECT id, name, phone, email, message, created_at 
        FROM enquiries 
        ORDER BY created_at DESC";

$result = $conn->query($sql);

$enquiries = [];

while ($row = $result->fetch_assoc()) {
    $row['date'] = date("M d, Y h:i A", strtotime($row['created_at']));
    $enquiries[] = $row;
}

echo json_encode([
    "status" => true,
    "data" => $enquiries
]);

$conn->close();
