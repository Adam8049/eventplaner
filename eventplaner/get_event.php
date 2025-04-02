<?php
session_start();
if (!isset($_SESSION["user_id"])) {
  http_response_code(403);
  exit("Nicht eingeloggt");
}

$conn = new mysqli("localhost", "root", "", "eventplaner");

$id = $_GET["id"];
$user_id = $_SESSION["user_id"];

$stmt = $conn->prepare("SELECT * FROM events WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();

$result = $stmt->get_result();
$event = $result->fetch_assoc();

if ($event) {
  echo json_encode($event);
} else {
  http_response_code(403);
  echo json_encode(["error" => "Kein Zugriff auf diesen Event."]);
}
?>
