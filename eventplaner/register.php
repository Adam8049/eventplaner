<?php
$conn = new mysqli("localhost", "root", "", "eventplaner");

$username = $_POST["username"];
$password = password_hash($_POST["password"], PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
$stmt->bind_param("ss", $username, $password);
$stmt->execute();

echo "ok";
?>
