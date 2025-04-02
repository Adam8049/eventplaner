<?php
session_start();
if (!isset($_SESSION["user_id"])) exit("Nicht eingeloggt");

$conn = new mysqli("localhost", "root", "", "eventplaner");

$user_id = $_SESSION["user_id"];
$id = $_POST["id"] ?? null;
$datum = $_POST["datum"];
$uhrzeit = $_POST["uhrzeit"];
$ort = $_POST["ort"];
$beschreibung = $_POST["beschreibung"];

if ($id) {
    $stmt = $conn->prepare("UPDATE events SET datum=?, uhrzeit=?, ort=?, beschreibung=? WHERE id=? AND user_id=?");
    $stmt->bind_param("ssssii", $datum, $uhrzeit, $ort, $beschreibung, $id, $user_id);
} else {
    $stmt = $conn->prepare("INSERT INTO events (datum, uhrzeit, ort, beschreibung, user_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $datum, $uhrzeit, $ort, $beschreibung, $user_id);
}

$stmt->execute();
?>
