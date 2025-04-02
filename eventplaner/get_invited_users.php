<?php
session_start();
if (!isset($_SESSION["user_id"])) {
  http_response_code(403);
  exit("Nicht eingeloggt");
}

$conn = new mysqli("localhost", "root", "", "eventplaner");

$event_id = $_GET["event_id"] ?? null;
if (!$event_id) {
  http_response_code(400);
  exit("Kein Event-ID übergeben");
}

$stmt = $conn->prepare("
  SELECT u.username, inviter.username AS invited_by_name
  FROM invited_users iu
  JOIN users u ON u.id = iu.user_id
  JOIN users inviter ON inviter.id = iu.invited_by
  WHERE iu.event_id = ?
");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = [
        "username" => $row["username"],
        "invited_by" => $row["invited_by_name"]
      ];
      
  }
  

echo json_encode($users);