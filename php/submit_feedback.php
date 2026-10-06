<?php

session_start();

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/db_connect.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);

    exit;
}

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$rating = (int) ($_POST["rating"] ?? 0);
$message = trim($_POST["feedback"] ?? "");

$student_id = isset($_SESSION["student_id"])
    ? (int) $_SESSION["student_id"]
    : null;


/* Validation */

if ($name === "") {
    echo json_encode([
        "success" => false,
        "message" => "Please enter your full name."
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        "success" => false,
        "message" => "Please enter a valid university email."
    ]);
    exit;
}

if ($rating < 1 || $rating > 5) {
    echo json_encode([
        "success" => false,
        "message" => "Please select a rating."
    ]);
    exit;
}

if ($message === "") {
    echo json_encode([
        "success" => false,
        "message" => "Please enter your feedback."
    ]);
    exit;
}

try {

    $stmt = $pdo->prepare("
        INSERT INTO feedback
        (
            student_id,
            name,
            email,
            rating,
            message
        )
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $student_id,
        $name,
        $email,
        $rating,
        $message
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Feedback submitted successfully."
    ]);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Database error while submitting feedback."
    ]);
}
?>