<?php
session_start();

require __DIR__ . "/db_connect.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Login PHP reached, but request is not POST.");
}

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

echo "<h2>LOGIN DEBUG</h2>";

echo "<p>Email received: " . htmlspecialchars($email) . "</p>";
echo "<p>Password received: " . (!empty($password) ? "YES" : "NO") . "</p>";

$stmt = $pdo->prepare(
    "SELECT id, name, email, password_hash
     FROM students
     WHERE email = :email
     LIMIT 1"
);

$stmt->execute([
    ":email" => $email
]);

$student = $stmt->fetch();

if (!$student) {
    die("<h3 style='color:red'>STUDENT NOT FOUND</h3>");
}

echo "<p>Student found: " . htmlspecialchars($student["name"]) . "</p>";

if (!password_verify($password, $student["password_hash"])) {
    die("<h3 style='color:red'>PASSWORD VERIFY FAILED</h3>");
}

echo "<p style='color:green'>PASSWORD VERIFIED</p>";

session_regenerate_id(true);

$_SESSION["student_id"] = (int)$student["id"];
$_SESSION["student_name"] = $student["name"];
$_SESSION["student_email"] = $student["email"];

echo "<h3 style='color:green'>SESSION CREATED</h3>";

echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<br>";
echo "<a href='session_test.php'>Open Session Test</a>";
?>