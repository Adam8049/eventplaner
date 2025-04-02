<?php
session_start();
$conn = new mysqli("localhost", "root", "", "eventplaner");

if (!isset($_SESSION["user_id"])) {
  http_response_code(403);
  exit("Nicht eingeloggt");
}

$suchbegriff = $_GET["q"] ?? "";
$user_id = $_SESSION["user_id"];

$stmt = $conn->prepare("SELECT username FROM users WHERE username LIKE CONCAT(?, '%') AND id != ?");
$stmt->bind_param("si", $suchbegriff, $user_id);
$stmt->execute();

$result = $stmt->get_result();
$vorschlaege = [];

while ($row = $result->fetch_assoc()) {
  $vorschlaege[] = $row["username"];
}

echo json_encode($vorschlaege);
?>
