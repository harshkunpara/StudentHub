
<?php

require_once __DIR__ . "/db_connect.php";

header("Content-Type: application/json; charset=UTF-8");

try {

    $stmt = $pdo->query("
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
        ORDER BY event_date ASC, event_time ASC
    ");

    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "events" => $events
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to load events."
    ]);
}
?>