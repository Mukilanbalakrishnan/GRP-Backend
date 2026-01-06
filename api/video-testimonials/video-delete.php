<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

$conn = new mysqli("localhost","root","","grp");

$id = $_POST['id'];
$conn->query("DELETE FROM video_testimonials WHERE id=$id");

echo json_encode(["status"=>true]);
