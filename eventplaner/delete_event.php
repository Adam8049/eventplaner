<?php
session_start();
$conn = new mysqli("localhost", "root", "", "eventplaner");

if (!isset($_SESSION["user_id"])) {
  http_response_code(403);
  exit("Nicht eingeloggt");
}

$user_id = $_SESSION["user_id"];
$id = $_GET["id"] ?? null;

if (!$id) {
  http_response_code(400);
  exit("Keine Event-ID");
}

// 1. Lösche zuerst alle Einladungen zu diesem Event
$stmt = $conn->prepare("DELETE FROM invited_users WHERE event_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

// 2. Jetzt Event löschen (nur wenn er dir gehört)
$stmt = $conn->prepare("DELETE FROM events WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
  echo "OK";
} else {
  http_response_code(403);
  echo "Du darfst diesen Event nicht löschen.";
}
