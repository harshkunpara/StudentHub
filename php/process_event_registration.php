<?php
session_start();

require_once "db_connect.php";

// Student login check
if (!isset($_SESSION['student_id'])) {
    header("Location: ../pages/login.html");
    exit;
}

// Event ID check
if (!isset($_GET['event_id']) || !is_numeric($_GET['event_id'])) {
    die("Invalid event ID.");
}

$student_id = $_SESSION['student_id'];
$event_id = (int) $_GET['event_id'];

try {
    // Check if event exists
    $check = $pdo->prepare("SELECT event_id FROM events WHERE event_id = ?");
    $check->execute([$event_id]);

    if (!$check->fetch()) {
        die("Event not found.");
    }

    // Register student
    $stmt = $pdo->prepare(
        "INSERT INTO registrations (student_id, event_id)
         VALUES (?, ?)"
    );

    $stmt->execute([$student_id, $event_id]);

    header("Location: ../pages/events.html?registered=success");
    exit;

} catch (PDOException $e) {

    // Duplicate registration
    if ($e->getCode() == 23000) {
        header("Location: ../pages/events.html?registered=already");
        exit;
    }

    die("Registration failed: " . $e->getMessage());
}
?>