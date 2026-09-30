<?php
require __DIR__ . "/db_connect.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../pages/registar.html");
    exit;
}

$name = trim($_POST["fullname"] ?? "");
$email = trim($_POST["email"] ?? "");
$mobile = trim($_POST["phone"] ?? "");
$enrollment = trim($_POST["enrollment_number"] ?? "");
$department = trim($_POST["department"] ?? "");
$semesterText = trim($_POST["semester"] ?? "");
$password = $_POST["password"] ?? "";
$confirmPassword = $_POST["confirm_password"] ?? "";

$errors = [];

if ($name === "") $errors[] = "Full name is required.";
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Enter a valid email address.";
if ($mobile === "" || !preg_match('/^[0-9+ ()-]{10,20}$/', $mobile)) $errors[] = "Enter a valid phone number.";
if ($enrollment === "") $errors[] = "Enrollment number is required.";
if ($department === "") $errors[] = "Department is required.";
if (!preg_match('/^Semester [1-8]$/', $semesterText)) $errors[] = "Select a valid semester.";
if (strlen($password) < 8) $errors[] = "Password must contain at least 8 characters.";
if ($password !== $confirmPassword) $errors[] = "Passwords do not match.";

$semester = (int) str_replace("Semester ", "", $semesterText);

if (!$errors) {
    $courseStmt = $pdo->prepare("SELECT course_id FROM courses WHERE course_name = :course_name");
    $courseStmt->execute([":course_name" => $department]);
    $courseId = $courseStmt->fetchColumn();

    if (!$courseId) {
        $errors[] = "Selected department is not available in the database.";
    }
}

if (!$errors) {
    $check = $pdo->prepare("SELECT id FROM students WHERE email = :email OR enrollment_number = :enrollment LIMIT 1");
    $check->execute([":email" => $email, ":enrollment" => $enrollment]);

    if ($check->fetch()) {
        $errors[] = "Email or enrollment number is already registered.";
    }
}

if ($errors) {
    http_response_code(400);
    echo "<!doctype html><html><head><meta charset='UTF-8'><title>Registration Error</title></head><body>";
    echo "<h2>Registration could not be completed</h2><ul>";
    foreach ($errors as $error) {
        echo "<li>" . htmlspecialchars($error) . "</li>";
    }
    echo "</ul><a href='../pages/registar.html'>Go back to registration</a></body></html>";
    exit;
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO students
        (name, email, mobile, enrollment_number, course_id, semester, password_hash)
        VALUES
        (:name, :email, :mobile, :enrollment_number, :course_id, :semester, :password_hash)";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ":name" => $name,
    ":email" => $email,
    ":mobile" => $mobile,
    ":enrollment_number" => $enrollment,
    ":course_id" => $courseId,
    ":semester" => $semester,
    ":password_hash" => $passwordHash
]);

header("Location: registration_success.php");
exit;
?>
