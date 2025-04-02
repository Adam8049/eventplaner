<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    http_response_code(403);
    exit;
}

$conn = new mysqli("localhost", "root", "", "eventplaner");

$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

echo json_encode($user);
?>
