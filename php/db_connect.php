<?php

$host = "sql212.infinityfree.com";
$dbname = "if0_43115119_studenthub";
$username = "if0_43115119";
$password = " ";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    die("Database connection failed: " . htmlspecialchars($e->getMessage()));
}

?>