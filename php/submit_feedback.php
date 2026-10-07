<?php

session_start();

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/db_connect.php";


// =====================================================
// REQUEST METHOD CHECK
// =====================================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);

    exit;
}


// =====================================================
// GET FORM DATA
// =====================================================

$name = trim($_POST["name"] ?? "");

$email = trim($_POST["email"] ?? "");

$rating = (int)($_POST["rating"] ?? 0);

$message = trim($_POST["feedback"] ?? "");


// =====================================================
// GET STUDENT ID FROM SESSION
// =====================================================

$student_id = isset($_SESSION["student_id"])
    ? (int)$_SESSION["student_id"]
    : null;


// =====================================================
// VALIDATION
// =====================================================

if ($name === "") {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Please enter your full name."
    ]);

    exit;
}


if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Please enter a valid university email."
    ]);

    exit;
}


if ($rating < 1 || $rating > 5) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Please select a rating between 1 and 5."
    ]);

    exit;
}


if ($message === "") {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Please enter your feedback."
    ]);

    exit;
}


// =====================================================
// CHECK WHETHER SESSION STUDENT STILL EXISTS
// =====================================================

try {

    if ($student_id !== null) {

        $checkStudent = $pdo->prepare("
            SELECT id
            FROM students
            WHERE id = ?
            LIMIT 1
        ");

        $checkStudent->execute([$student_id]);

        $studentExists = $checkStudent->fetchColumn();

        // Student was deleted / session contains old ID
        if (!$studentExists) {
            $student_id = null;
        }
    }


    // =================================================
    // INSERT FEEDBACK
    // =================================================

    $stmt = $pdo->prepare("
        INSERT INTO feedback
        (
            student_id,
            name,
            email,
            rating,
            message
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            ?
        )
    ");

    $stmt->execute([
        $student_id,
        $name,
        $email,
        $rating,
        $message
    ]);


    // =================================================
    // SUCCESS
    // =================================================

    echo json_encode([
        "success" => true,
        "message" => "Feedback submitted successfully."
    ]);

    exit;


} catch (PDOException $e) {

    // =================================================
    // DATABASE ERROR
    // =================================================

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Database error while submitting feedback.",
        "error" => $e->getMessage()
    ]);

    exit;
}

?>