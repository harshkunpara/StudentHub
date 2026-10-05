<?php

session_start();


// ==========================================
// ADMIN ACCESS PROTECTION
// ==========================================

if (
    !isset($_SESSION["admin_logged_in"]) ||
    $_SESSION["admin_logged_in"] !== true
) {

    http_response_code(401);

    header("Content-Type: application/json; charset=UTF-8");

    echo json_encode([
        "success" => false,
        "message" => "Unauthorized admin access."
    ]);

    exit;
}


// ==========================================
// DATABASE
// ==========================================

require_once __DIR__ . "/db_connect.php";

header("Content-Type: application/json; charset=UTF-8");


try {

    /* ==========================================
       TOTAL COUNTS
    ========================================== */

    $studentStmt = $pdo->query(
        "SELECT COUNT(*) FROM students"
    );

    $totalStudents = (int) $studentStmt->fetchColumn();


    $eventStmt = $pdo->query(
        "SELECT COUNT(*) FROM events"
    );

    $totalEvents = (int) $eventStmt->fetchColumn();


    $registrationStmt = $pdo->query(
        "SELECT COUNT(*) FROM registrations"
    );

    $totalRegistrations = (int) $registrationStmt->fetchColumn();


    $courseStmt = $pdo->query(
        "SELECT COUNT(*) FROM courses"
    );

    $totalCourses = (int) $courseStmt->fetchColumn();


    /* ==========================================
       STUDENTS
    ========================================== */

    $studentsStmt = $pdo->query(
        "SELECT
            s.id,
            s.name,
            s.email,
            s.mobile,
            s.enrollment_number,
            s.semester,
            s.gender,
            s.city,
            s.created_at,
            c.course_name
        FROM students s
        LEFT JOIN courses c
            ON s.course_id = c.course_id
        ORDER BY s.created_at DESC"
    );

    $students = $studentsStmt->fetchAll(PDO::FETCH_ASSOC);


    /* ==========================================
       EVENTS
    ========================================== */

    $eventsStmt = $pdo->query(
        "SELECT
            e.event_id,
            e.title,
            e.event_date,
            e.location,
            e.category,
            COUNT(r.reg_id) AS registration_count
        FROM events e
        LEFT JOIN registrations r
            ON e.event_id = r.event_id
        GROUP BY
            e.event_id,
            e.title,
            e.event_date,
            e.location,
            e.category
        ORDER BY e.event_date ASC"
    );

    $events = $eventsStmt->fetchAll(PDO::FETCH_ASSOC);


    /* ==========================================
       RECENT EVENT REGISTRATIONS
    ========================================== */

    $registrationsStmt = $pdo->query(
        "SELECT
            r.reg_id,
            r.registered_on,
            s.name AS student_name,
            s.enrollment_number,
            e.title AS event_title
        FROM registrations r
        INNER JOIN students s
            ON r.student_id = s.id
        INNER JOIN events e
            ON r.event_id = e.event_id
        ORDER BY r.registered_on DESC
        LIMIT 20"
    );

    $registrations =
        $registrationsStmt->fetchAll(PDO::FETCH_ASSOC);


    /* ==========================================
       COURSES
    ========================================== */

    $coursesStmt = $pdo->query(
        "SELECT
            course_id,
            course_name
        FROM courses
        ORDER BY course_name"
    );

    $courses = $coursesStmt->fetchAll(PDO::FETCH_ASSOC);


    /* ==========================================
       RESPONSE
    ========================================== */

    echo json_encode(
        [
            "success" => true,

            "statistics" => [
                "students" => $totalStudents,
                "events" => $totalEvents,
                "registrations" => $totalRegistrations,
                "courses" => $totalCourses
            ],

            "students" => $students,

            "events" => $events,

            "registrations" => $registrations,

            "courses" => $courses
        ],
        JSON_UNESCAPED_UNICODE
    );

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode(
        [
            "success" => false,
            "message" => "Database error.",
            "error" => $e->getMessage()
        ]
    );
}