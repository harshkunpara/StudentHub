<?php
// ============================================================
// Practical 7 - StudentHub Registration Backend
// Server-side validation + CSV storage
// ============================================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request.");
}

// ------------------------------------------------------------
// 1. Read form data
// ------------------------------------------------------------
$name = trim($_POST["fullname"] ?? "");
$email = trim($_POST["email"] ?? "");
$mobile = trim($_POST["phone"] ?? "");
$enrollment = trim($_POST["enrollment_number"] ?? "");
$department = trim($_POST["department"] ?? "");
$semester = trim($_POST["semester"] ?? "");
$password = $_POST["password"] ?? "";
$confirmPassword = $_POST["confirm_password"] ?? "";
$gender = trim($_POST["gender"] ?? "");
$terms = isset($_POST["terms"]);

$errors = [];

// ------------------------------------------------------------
// 2. Server-side validation
// ------------------------------------------------------------
if ($name === "") {
    $errors[] = "Name is required.";
} elseif (!preg_match("/^[A-Za-z ]+$/", $name)) {
    $errors[] = "Name should contain letters and spaces only.";
}

if ($email === "") {
    $errors[] = "Email is required.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Please enter a valid email address.";
}

if ($mobile === "") {
    $errors[] = "Phone number is required.";
} elseif (!preg_match("/^[6-9][0-9]{9}$/", $mobile)) {
    $errors[] = "Please enter a valid 10-digit mobile number.";
}

if ($enrollment === "") {
    $errors[] = "Enrollment number is required.";
} elseif (!preg_match("/^[A-Za-z0-9-]+$/", $enrollment)) {
    $errors[] = "Enrollment number contains invalid characters.";
}

$allowedDepartments = [
    "Information Technology",
    "Computer Engineering",
    "Mechanical Engineering",
    "Civil Engineering"
];

if ($department === "") {
    $errors[] = "Please select your department.";
} elseif (!in_array($department, $allowedDepartments, true)) {
    $errors[] = "Invalid department selected.";
}

$allowedSemesters = [
    "Semester 1", "Semester 2", "Semester 3", "Semester 4",
    "Semester 5", "Semester 6", "Semester 7", "Semester 8"
];

if ($semester === "") {
    $errors[] = "Please select your semester.";
} elseif (!in_array($semester, $allowedSemesters, true)) {
    $errors[] = "Invalid semester selected.";
}

if ($gender === "") {
    $errors[] = "Please select your gender.";
} elseif (!in_array($gender, ["Male", "Female", "Other"], true)) {
    $errors[] = "Invalid gender selected.";
}

if ($password === "") {
    $errors[] = "Password is required.";
} elseif (
    strlen($password) < 8 ||
    !preg_match("/[A-Z]/", $password) ||
    !preg_match("/[a-z]/", $password) ||
    !preg_match("/[0-9]/", $password) ||
    !preg_match("/[@$!%*?&]/", $password)
) {
    $errors[] = "Password must contain at least 8 characters, one uppercase letter, one lowercase letter, one number and one special character.";
}

if ($confirmPassword === "") {
    $errors[] = "Please confirm your password.";
} elseif ($password !== $confirmPassword) {
    $errors[] = "Passwords do not match.";
}

if (!$terms) {
    $errors[] = "You must accept the Terms and Conditions.";
}

// ------------------------------------------------------------
// 3. Show all errors and stop
// ------------------------------------------------------------
if (count($errors) > 0) {
    echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Registration Failed</title>";
    echo "<style>
        body{font-family:Arial,sans-serif;background:#f5f7fb;padding:40px}
        .card{max-width:700px;margin:auto;background:#fff;padding:30px;border-radius:16px;box-shadow:0 10px 30px rgba(0,0,0,.08)}
        h1{color:#c62828}.error{margin:8px 0}.back{display:inline-block;margin-top:18px;padding:10px 16px;background:#4f46e5;color:#fff;text-decoration:none;border-radius:8px}
    </style></head><body><div class='card'>";
    echo "<h1>Registration Failed</h1><ul>";
    foreach ($errors as $error) {
        echo "<li class='error'>" . htmlspecialchars($error, ENT_QUOTES, "UTF-8") . "</li>";
    }
    echo "</ul><a class='back' href='../pages/registar.html'>Go Back</a>";
    echo "</div></body></html>";
    exit;
}

// ------------------------------------------------------------
// 4. Store validated raw values; escape only when displaying
// ------------------------------------------------------------
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$csvFile = "../data/registrations.csv";

// Create CSV with header on first registration
if (!file_exists($csvFile)) {
    $file = fopen($csvFile, "w");

    if ($file === false) {
        die("Unable to create registration storage file.");
    }

    fputcsv($file, [
        "Name",
        "Email",
        "Phone",
        "Enrollment Number",
        "Department",
        "Semester",
        "Gender",
        "Password"
    ]);

    fclose($file);
}

// ------------------------------------------------------------
// 5. Append registration safely
// ------------------------------------------------------------
$file = fopen($csvFile, "a");

if ($file === false) {
    die("Unable to open registration storage file.");
}

if (!flock($file, LOCK_EX)) {
    fclose($file);
    die("Unable to lock registration storage file.");
}

fputcsv($file, [
    $name,
    $email,
    $mobile,
    $enrollment,
    $department,
    $semester,
    $gender,
    $hashedPassword
]);

flock($file, LOCK_UN);
fclose($file);

// ------------------------------------------------------------
// 6. Success response
// ------------------------------------------------------------
echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Registration Successful</title>";
echo "<style>
    body{font-family:Arial,sans-serif;background:#f5f7fb;padding:40px}
    .card{max-width:700px;margin:auto;background:#fff;padding:30px;border-radius:16px;box-shadow:0 10px 30px rgba(0,0,0,.08)}
    h1{color:#188038}.btn{display:inline-block;margin:10px 8px 0 0;padding:11px 16px;background:#4f46e5;color:#fff;text-decoration:none;border-radius:8px}
</style></head><body><div class='card'>";
echo "<h1>Registration Successful!</h1>";
echo "<p>Your StudentHub registration has been submitted successfully.</p>";
echo "<p>Your password has been stored as a secure hash.</p>";
echo "<a class='btn' href='../pages/registar.html'>Back to Registration</a>";
echo "<a class='btn' href='view_registrations.php'>View Registered Students</a>";
echo "</div></body></html>";
?>
