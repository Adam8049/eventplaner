<?php
session_start();
if (!isset($_SESSION["user_id"])) {
  http_response_code(403);
  exit("Nicht eingeloggt");
}

$conn = new mysqli("localhost", "root", "", "eventplaner");

$username = $_POST["username"] ?? "";
$event_id = $_POST["event_id"] ?? "";
$invited_by = $_SESSION["user_id"];

if (!$username || !$event_id) {
  http_response_code(400);
  exit("Fehlende Daten");
}

// User-ID vom einzuladenden Benutzer holen
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();

if (!$user) {
  http_response_code(404);
  exit("Benutzer nicht gefunden");
}

$user_id = $user["id"];

// Prüfen, ob Einladung schon existiert
$stmt = $conn->prepare("SELECT id FROM invited_users WHERE event_id = ? AND user_id = ?");
$stmt->bind_param("ii", $event_id, $user_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
  http_response_code(409);
  exit("Benutzer wurde bereits eingeladen");
}

// Einladung speichern mit invited_by
$stmt = $conn->prepare("INSERT INTO invited_users (event_id, user_id, invited_by) VALUES (?, ?, ?)");
$stmt->bind_param("iii", $event_id, $user_id, $invited_by);
$stmt->execute();

echo "Erfolgreich eingeladen";
