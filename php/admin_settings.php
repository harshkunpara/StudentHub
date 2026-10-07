<?php

session_start();

header("Content-Type: application/json; charset=UTF-8");


// ===============================
// ADMIN ACCESS CHECK
// ===============================

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


// ===============================
// DATABASE CONNECTION
// ===============================

require_once __DIR__ . "/db_connect.php";


// ===============================
// GET SETTINGS
// ===============================

if ($_SERVER["REQUEST_METHOD"] === "GET") {

    try {

        $stmt = $pdo->query("
            SELECT
                setting_id,
                current_semester,
                semester_start_date,
                semester_end_date,
                maintenance_mode,
                updated_at
            FROM system_settings
            ORDER BY setting_id ASC
            LIMIT 1
        ");

        $settings = $stmt->fetch(PDO::FETCH_ASSOC);


        if (!$settings) {

            $insert = $pdo->prepare("
                INSERT INTO system_settings
                (
                    current_semester,
                    semester_start_date,
                    semester_end_date,
                    maintenance_mode
                )
                VALUES (?, ?, ?, ?)
            ");

            $insert->execute([
                "Semester 3",
                null,
                null,
                0
            ]);


            $settings = [
                "setting_id" => $pdo->lastInsertId(),
                "current_semester" => "Semester 3",
                "semester_start_date" => null,
                "semester_end_date" => null,
                "maintenance_mode" => 0,
                "updated_at" => null
            ];
        }


        echo json_encode([
            "success" => true,
            "settings" => $settings
        ]);

        exit;


    } catch (PDOException $e) {

        http_response_code(500);

        echo json_encode([
            "success" => false,
            "message" => "Database error.",
            "error" => $e->getMessage()
        ]);

        exit;
    }
}


// ===============================
// SAVE SETTINGS
// ===============================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $currentSemester =
        trim($_POST["current_semester"] ?? "");

    $startDate =
        trim($_POST["semester_start_date"] ?? "");

    $endDate =
        trim($_POST["semester_end_date"] ?? "");

    $maintenanceMode =
        isset($_POST["maintenance_mode"])
            ? (int)$_POST["maintenance_mode"]
            : 0;


    // ===============================
    // VALIDATION
    // ===============================

    $allowedSemesters = [
        "Semester 1",
        "Semester 2",
        "Semester 3",
        "Semester 4",
        "Semester 5",
        "Semester 6",
        "Semester 7",
        "Semester 8"
    ];


    if (!in_array($currentSemester, $allowedSemesters, true)) {

        http_response_code(400);

        echo json_encode([
            "success" => false,
            "message" => "Invalid semester selected."
        ]);

        exit;
    }


    if ($startDate === "") {
        $startDate = null;
    }


    if ($endDate === "") {
        $endDate = null;
    }


    if (
        $startDate !== null &&
        $endDate !== null &&
        $startDate > $endDate
    ) {

        http_response_code(400);

        echo json_encode([
            "success" => false,
            "message" => "Semester end date cannot be before start date."
        ]);

        exit;
    }


    $maintenanceMode =
        $maintenanceMode === 1 ? 1 : 0;


    // ===============================
    // CHECK EXISTING SETTINGS
    // ===============================

    try {

        $check = $pdo->query("
            SELECT setting_id
            FROM system_settings
            ORDER BY setting_id ASC
            LIMIT 1
        ");

        $existing = $check->fetch(PDO::FETCH_ASSOC);


        if ($existing) {

            // ===============================
            // UPDATE
            // ===============================

            $stmt = $pdo->prepare("
                UPDATE system_settings
                SET
                    current_semester = ?,
                    semester_start_date = ?,
                    semester_end_date = ?,
                    maintenance_mode = ?
                WHERE setting_id = ?
            ");

            $stmt->execute([
                $currentSemester,
                $startDate,
                $endDate,
                $maintenanceMode,
                $existing["setting_id"]
            ]);

        } else {

            // ===============================
            // INSERT
            // ===============================

            $stmt = $pdo->prepare("
                INSERT INTO system_settings
                (
                    current_semester,
                    semester_start_date,
                    semester_end_date,
                    maintenance_mode
                )
                VALUES (?, ?, ?, ?)
            ");

            $stmt->execute([
                $currentSemester,
                $startDate,
                $endDate,
                $maintenanceMode
            ]);
        }


        echo json_encode([
            "success" => true,
            "message" => "System settings updated successfully."
        ]);

        exit;


    } catch (PDOException $e) {

        http_response_code(500);

        echo json_encode([
            "success" => false,
            "message" => "Database error.",
            "error" => $e->getMessage()
        ]);

        exit;
    }
}


// ===============================
// INVALID REQUEST
// ===============================

http_response_code(405);

echo json_encode([
    "success" => false,
    "message" => "Method not allowed."
]);

?>