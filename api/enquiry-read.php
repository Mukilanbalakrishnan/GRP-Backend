<?php
header("Content-Type: application/json");
include "config.php";

$res = mysqli_query($conn, "SELECT * FROM enquiries ORDER BY id DESC");
$data = [];

while ($row = mysqli_fetch_assoc($res)) {
    $data[] = $row;
}

echo json_encode($data);
?>
