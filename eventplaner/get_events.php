<?php
session_start();
if (!isset($_SESSION["user_id"])) exit("Nicht eingeloggt");

$conn = new mysqli("localhost", "root", "", "eventplaner");

$user_id = $_SESSION["user_id"];
$stmt = $conn->prepare("SELECT * FROM events WHERE user_id = ? ORDER BY datum, uhrzeit");
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$events = [];

while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}

echo json_encode($events);
?>
