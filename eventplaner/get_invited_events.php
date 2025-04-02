<?php
session_start();
$conn = new mysqli("localhost", "root", "", "eventplaner");

if (!isset($_SESSION["user_id"])) {
    http_response_code(403);
    exit("Nicht eingeloggt");
}

$user_id = $_SESSION["user_id"];

$stmt = $conn->prepare("
SELECT events.*, inviter.username AS invited_by
FROM invited_users
JOIN events ON invited_users.event_id = events.id
JOIN users inviter ON invited_users.invited_by = inviter.id
WHERE invited_users.user_id = ?
  ORDER BY datum, uhrzeit
");
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$events = [];

while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}

echo json_encode($events);
?>
