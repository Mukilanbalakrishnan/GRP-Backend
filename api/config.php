<?php
$host = "localhost";
$user = "root";
$pass = "";        // keep empty for XAMPP
$db   = "grp";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    echo json_encode([
        "status" => false,
        "message" => "Database connection failed"
    ]);
    exit;
}
?>
