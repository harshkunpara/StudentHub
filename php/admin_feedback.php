<?php

session_start();

header("Content-Type: application/json; charset=UTF-8");

if (
    !isset($_SESSION["admin_logged_in"]) ||
    $_SESSION["admin_logged_in"] !== true
) {
    http_response_code(401);

    echo json_encode([
        "success" => false,
        "message" => "Admin login required."
    ]);

    exit;
}

require_once __DIR__ . "/db_connect.php";

try {

    $stmt = $pdo->query("
        SELECT
            f.feedback_id,
            f.name,
            f.email,
            f.rating,
            f.message,
            f.submitted_at,
            s.enrollment_number
        FROM feedback f
        LEFT JOIN students s
            ON f.student_id = s.id
        ORDER BY f.submitted_at DESC
    ");

    $feedback = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "feedback" => $feedback
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to load feedback."
    ]);
}
?>