<?php

session_start();

header("Content-Type: application/json; charset=UTF-8");


// ==========================================
// CHECK LOGIN
// ==========================================

if (!isset($_SESSION["student_id"])) {

    http_response_code(401);

    echo json_encode([
        "success" => false,
        "message" => "Login required."
    ]);

    exit;
}


// ==========================================
// DATABASE
// ==========================================

require_once __DIR__ . "/db_connect.php";


// ==========================================
// ONLY POST
// ==========================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);

    exit;
}


// ==========================================
// GET CURRENT LOGGED-IN STUDENT
// ==========================================

$student_id = (int) $_SESSION["student_id"];


// ==========================================
// GET FORM DATA
// ==========================================

$attendance = trim($_POST["attendance"] ?? "");
$cgpa       = trim($_POST["cgpa"] ?? "");


// ==========================================
// VALIDATION
// ==========================================

if ($attendance === "" || $cgpa === "") {

    echo json_encode([
        "success" => false,
        "message" => "Please enter Attendance and CGPA."
    ]);

    exit;
}


if (!is_numeric($attendance)) {

    echo json_encode([
        "success" => false,
        "message" => "Attendance must be a number."
    ]);

    exit;
}


if (!is_numeric($cgpa)) {

    echo json_encode([
        "success" => false,
        "message" => "CGPA must be a number."
    ]);

    exit;
}


$attendance = (float) $attendance;
$cgpa       = (float) $cgpa;


// Attendance validation

if ($attendance < 0 || $attendance > 100) {

    echo json_encode([
        "success" => false,
        "message" => "Attendance must be between 0 and 100."
    ]);

    exit;
}


// CGPA validation

if ($cgpa < 0 || $cgpa > 10) {

    echo json_encode([
        "success" => false,
        "message" => "CGPA must be between 0 and 10."
    ]);

    exit;
}


// ==========================================
// UPDATE DATABASE
// ==========================================

try {

    $stmt = $pdo->prepare("
        UPDATE students
        SET
            attendance = ?,
            cgpa = ?
        WHERE id = ?
    ");


    $stmt->execute([
        $attendance,
        $cgpa,
        $student_id
    ]);


    // ==========================================
    // VERIFY UPDATE
    // ==========================================

    $check = $pdo->prepare("
        SELECT attendance, cgpa
        FROM students
        WHERE id = ?
        LIMIT 1
    ");


    $check->execute([
        $student_id
    ]);


    $student = $check->fetch(PDO::FETCH_ASSOC);


    if (!$student) {

        echo json_encode([
            "success" => false,
            "message" => "Student record not found."
        ]);

        exit;
    }


    echo json_encode([

        "success" => true,

        "message" => "Academic details saved successfully.",

        "attendance" => (float) $student["attendance"],

        "cgpa" => (float) $student["cgpa"]

    ]);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([

        "success" => false,

        "message" => "Database error while saving academic details."

    ]);

}

?>