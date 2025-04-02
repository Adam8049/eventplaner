<?php
session_start();
$conn = new mysqli("localhost", "root", "", "eventplaner");

$username = $_POST["username"];
$password = $_POST["password"];

$stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user["password"])) {
    $_SESSION["user_id"] = $user["id"];
    echo "ok";
} else {
    http_response_code(401);
    echo "Login fehlgeschlagen";
}
?>
