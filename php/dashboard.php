<?php

session_start();

header("Content-Type: application/json; charset=UTF-8");

if (!isset($_SESSION["student_id"])) {

    http_response_code(401);

    echo json_encode([
        "success" => false,
        "message" => "Login required.",
        "session" => $_SESSION
    ]);

    exit;
}

require_once __DIR__ . "/db_connect.php";

$student_id = (int) $_SESSION["student_id"];

try {

    // STUDENT DATA
    $stmt = $pdo->prepare("
        SELECT
    s.id,
    s.name,
    s.email,
    s.mobile,
    s.enrollment_number,
    s.semester,
    s.attendance,
    s.cgpa,
    c.course_name
FROM students s
        LEFT JOIN courses c
            ON s.course_id = c.course_id
        WHERE s.id = ?
        LIMIT 1
    ");

    $stmt->execute([$student_id]);

    $student = $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$student) {

        http_response_code(404);

        echo json_encode([
            "success" => false,
            "message" => "Student record not found.",
            "student_id" => $student_id
        ]);

        exit;
    }


    // COURSES
    $courseStmt = $pdo->query("
        SELECT
            course_id,
            course_name
        FROM courses
        ORDER BY course_id ASC
    ");

    $courses = $courseStmt->fetchAll(PDO::FETCH_ASSOC);


    // REGISTERED EVENTS
    $eventStmt = $pdo->prepare("
        SELECT
            e.event_id,
            e.title,
            e.event_date,
            e.location,
            e.category
        FROM registrations r
        INNER JOIN events e
            ON r.event_id = e.event_id
        WHERE r.student_id = ?
        ORDER BY e.event_date ASC
    ");

    $eventStmt->execute([$student_id]);

    $registeredEvents = $eventStmt->fetchAll(PDO::FETCH_ASSOC);


    // FINAL RESPONSE
    echo json_encode([
        "success" => true,
"student" => [
    "id" => (int)$student["id"],
    "name" => $student["name"],
    "email" => $student["email"],
    "mobile" => $student["mobile"],
    "enrollment_number" => $student["enrollment_number"],
    "semester" => $student["semester"],
    "attendance" => $student["attendance"],
    "cgpa" => $student["cgpa"],
    "department" => $student["course_name"] ?? "N/A"
],

        "courses" => $courses,

        "registered_events" => $registeredEvents

    ], JSON_UNESCAPED_UNICODE);


} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([

        "success" => false,

        "message" => "DATABASE ERROR",

        "error" => $e->getMessage(),

        "student_id" => $student_id

    ]);

}

?>