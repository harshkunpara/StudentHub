<?php

session_start();

header("Content-Type: application/json; charset=UTF-8");


// ==========================================
// ADMIN CREDENTIALS
// ==========================================

$ADMIN_USERNAME = "admin";
$ADMIN_PASSWORD = "StudentHub@2026";


// ==========================================
// ONLY POST REQUEST
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
// GET INPUT
// ==========================================

$username =
    trim($_POST["username"] ?? "");

$password =
    $_POST["password"] ?? "";


// ==========================================
// CHECK CREDENTIALS
// ==========================================

if (
    $username === $ADMIN_USERNAME &&
    $password === $ADMIN_PASSWORD
) {

    session_regenerate_id(true);

    $_SESSION["admin_logged_in"] = true;

    $_SESSION["admin_username"] =
        $ADMIN_USERNAME;


    echo json_encode([
        "success" => true,
        "message" => "Admin login successful."
    ]);

    exit;
}


// ==========================================
// WRONG CREDENTIALS
// ==========================================

http_response_code(401);

echo json_encode([
    "success" => false,
    "message" => "Invalid admin username or password."
]);

exit;

?>