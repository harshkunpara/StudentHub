<?php

session_start();

header("Content-Type: application/json; charset=UTF-8");


// ==========================================
// ADMIN ACCESS PROTECTION
// ==========================================

if (
    !isset($_SESSION["admin_logged_in"]) ||
    $_SESSION["admin_logged_in"] !== true
) {
    http_response_code(401);

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


try {


    // ==========================================
    // GET RECORDS
    // ==========================================

    if ($_SERVER["REQUEST_METHOD"] === "GET") {


        // ------------------------------
        // STUDENTS
        // ------------------------------

        $studentStmt = $pdo->query("
            SELECT
                s.id,
                s.name,
                s.email,
                s.enrollment_number,
                s.semester,
                s.attendance,
                s.cgpa,
                c.course_name
            FROM students s
            LEFT JOIN courses c
                ON s.course_id = c.course_id
            ORDER BY s.id DESC
        ");

        $students =
            $studentStmt->fetchAll(PDO::FETCH_ASSOC);


        // ------------------------------
        // EVENTS
        // ------------------------------

        $eventStmt = $pdo->query("
            SELECT
                event_id,
                title,
                event_date,
                event_time,
                location,
                category,
                description,
                image_path
            FROM events
            ORDER BY event_date DESC, event_id DESC
        ");

        $events =
            $eventStmt->fetchAll(PDO::FETCH_ASSOC);


        echo json_encode([
            "success" => true,
            "students" => $students,
            "events" => $events
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }



    // ==========================================
    // DELETE RECORD
    // ==========================================

    if ($_SERVER["REQUEST_METHOD"] === "POST") {


        $type = trim($_POST["type"] ?? "");

        $id = (int) ($_POST["id"] ?? 0);


        if (!in_array($type, ["student", "event"], true)) {

            http_response_code(400);

            echo json_encode([
                "success" => false,
                "message" => "Invalid record type."
            ]);

            exit;
        }


        if ($id <= 0) {

            http_response_code(400);

            echo json_encode([
                "success" => false,
                "message" => "Invalid record ID."
            ]);

            exit;
        }



        // ==========================================
        // DELETE STUDENT
        // ==========================================

        if ($type === "student") {


            // Check student exists

            $checkStmt = $pdo->prepare("
                SELECT id, name
                FROM students
                WHERE id = ?
                LIMIT 1
            ");

            $checkStmt->execute([$id]);

            $student =
                $checkStmt->fetch(PDO::FETCH_ASSOC);


            if (!$student) {

                http_response_code(404);

                echo json_encode([
                    "success" => false,
                    "message" => "Student not found."
                ]);

                exit;
            }


            $pdo->beginTransaction();


            // Delete student's event registrations

            $registrationStmt = $pdo->prepare("
                DELETE FROM registrations
                WHERE student_id = ?
            ");

            $registrationStmt->execute([$id]);


            // Delete student

            $deleteStmt = $pdo->prepare("
                DELETE FROM students
                WHERE id = ?
            ");

            $deleteStmt->execute([$id]);


            $pdo->commit();


            echo json_encode([
                "success" => true,
                "message" =>
                    "Student '" .
                    $student["name"] .
                    "' deleted successfully."
            ]);

            exit;
        }



        // ==========================================
        // DELETE EVENT
        // ==========================================

        if ($type === "event") {


            // Get event information first

            $checkStmt = $pdo->prepare("
                SELECT
                    event_id,
                    title,
                    image_path
                FROM events
                WHERE event_id = ?
                LIMIT 1
            ");

            $checkStmt->execute([$id]);

            $event =
                $checkStmt->fetch(PDO::FETCH_ASSOC);


            if (!$event) {

                http_response_code(404);

                echo json_encode([
                    "success" => false,
                    "message" => "Event not found."
                ]);

                exit;
            }


            $pdo->beginTransaction();


            // Delete registrations for this event

            $registrationStmt = $pdo->prepare("
                DELETE FROM registrations
                WHERE event_id = ?
            ");

            $registrationStmt->execute([$id]);


            // Delete event

            $deleteStmt = $pdo->prepare("
                DELETE FROM events
                WHERE event_id = ?
            ");

            $deleteStmt->execute([$id]);


            $pdo->commit();



            // ==========================================
            // DELETE EVENT IMAGE FROM UPLOADS
            // ==========================================

            if (!empty($event["image_path"])) {

                $imageFile =
                    __DIR__ .
                    "/../" .
                    $event["image_path"];


                if (is_file($imageFile)) {

                    @unlink($imageFile);

                }
            }


            echo json_encode([
                "success" => true,
                "message" =>
                    "Event '" .
                    $event["title"] .
                    "' deleted successfully."
            ]);

            exit;
        }
    }



    // ==========================================
    // INVALID REQUEST
    // ==========================================

    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);


} catch (PDOException $e) {


    // ==========================================
    // ROLLBACK
    // ==========================================

    if ($pdo->inTransaction()) {

        $pdo->rollBack();

    }


    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Database error.",
        "error" => $e->getMessage()
    ]);
}

?>