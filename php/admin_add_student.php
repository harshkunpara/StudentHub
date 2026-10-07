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
    exit("Unauthorized admin access.");
}


// ==========================================
// DATABASE
// ==========================================

require_once __DIR__ . "/db_connect.php";


// ==========================================
// ONLY POST REQUEST
// ==========================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../pages/admin-add-student.html");
    exit;
}


// ==========================================
// GET FORM DATA
// ==========================================

$name = trim($_POST["fullname"] ?? "");
$email = trim($_POST["email"] ?? "");
$mobile = trim($_POST["phone"] ?? "");
$enrollment = trim($_POST["enrollment_number"] ?? "");
$department = trim($_POST["department"] ?? "");
$semesterText = trim($_POST["semester"] ?? "");
$password = $_POST["password"] ?? "";
$confirmPassword = $_POST["confirm_password"] ?? "";

$errors = [];


// ==========================================
// NAME VALIDATION
// ==========================================

if ($name === "") {

    $errors[] = "Full name is required.";

} elseif (!preg_match('/^[A-Za-z ]{2,50}$/', $name)) {

    $errors[] = "Name must contain only letters and spaces.";

}


// ==========================================
// EMAIL VALIDATION
// ==========================================

if ($email === "") {

    $errors[] = "Email is required.";

} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    $errors[] = "Enter a valid email address.";

}


// ==========================================
// MOBILE VALIDATION
// ==========================================

if ($mobile === "") {

    $errors[] = "Mobile number is required.";

} elseif (!preg_match('/^[0-9]{10}$/', $mobile)) {

    $errors[] = "Mobile number must contain exactly 10 digits.";

}


// ==========================================
// ENROLLMENT VALIDATION
// ==========================================

if ($enrollment === "") {

    $errors[] = "Enrollment number is required.";

} elseif (!preg_match('/^[0-9]{2}IT[0-9]{3}$/i', $enrollment)) {

    $errors[] = "Enter a valid enrollment number. Example: 25IT048.";

}


// ==========================================
// DEPARTMENT CHECK
// ==========================================

if ($department === "") {

    $errors[] = "Department is required.";

}


// ==========================================
// SEMESTER VALIDATION
// ==========================================

if (!preg_match('/^Semester [1-8]$/', $semesterText)) {

    $errors[] = "Select a valid semester.";

}

$semester = (int) str_replace("Semester ", "", $semesterText);


// ==========================================
// PASSWORD VALIDATION
// ==========================================

if ($password === "") {

    $errors[] = "Password is required.";

} elseif (strlen($password) < 8) {

    $errors[] = "Password must contain at least 8 characters.";

} elseif (!preg_match('/[A-Z]/', $password)) {

    $errors[] = "Password must contain at least one uppercase letter.";

} elseif (!preg_match('/[a-z]/', $password)) {

    $errors[] = "Password must contain at least one lowercase letter.";

} elseif (!preg_match('/[0-9]/', $password)) {

    $errors[] = "Password must contain at least one number.";

} elseif (!preg_match('/[^A-Za-z0-9]/', $password)) {

    $errors[] = "Password must contain at least one special character.";

}


// ==========================================
// CONFIRM PASSWORD
// ==========================================

if ($password !== $confirmPassword) {

    $errors[] = "Passwords do not match.";

}


// ==========================================
// CHECK DEPARTMENT IN DATABASE
// ==========================================

$courseId = null;

if (!$errors) {

    $courseStmt = $pdo->prepare(
        "SELECT course_id
         FROM courses
         WHERE course_name = :course_name
         LIMIT 1"
    );

    $courseStmt->execute([
        ":course_name" => $department
    ]);

    $courseId = $courseStmt->fetchColumn();

    if (!$courseId) {

        $errors[] = "Selected department is not available in the database.";

    }
}


// ==========================================
// CHECK DUPLICATE EMAIL / ENROLLMENT
// ==========================================

if (!$errors) {

    $checkStmt = $pdo->prepare(
        "SELECT id
         FROM students
         WHERE email = :email
            OR enrollment_number = :enrollment
         LIMIT 1"
    );

    $checkStmt->execute([
        ":email" => $email,
        ":enrollment" => $enrollment
    ]);

    if ($checkStmt->fetch()) {

        $errors[] =
            "Email or enrollment number is already registered.";

    }
}


// ==========================================
// SHOW VALIDATION ERRORS
// ==========================================

if ($errors) {

    echo "<!DOCTYPE html>";
    echo "<html lang='en'>";
    echo "<head>";
    echo "<meta charset='UTF-8'>";
    echo "<title>Admin - Add Student</title>";
    echo "</head>";
    echo "<body>";

    echo "<h2>Unable to Add Student</h2>";

    echo "<ul>";

    foreach ($errors as $error) {

        echo "<li>" .
             htmlspecialchars($error) .
             "</li>";

    }

    echo "</ul>";

    echo "<a href='../pages/admin-add-student.html'>";
    echo "Back to Add Student";
    echo "</a>";

    echo "</body>";
    echo "</html>";

    exit;
}


// ==========================================
// PASSWORD HASH
// ==========================================

$passwordHash = password_hash(
    $password,
    PASSWORD_DEFAULT
);


// ==========================================
// INSERT STUDENT
// ==========================================

$sql = "INSERT INTO students
        (
            name,
            email,
            mobile,
            enrollment_number,
            course_id,
            semester,
            password_hash
        )
        VALUES
        (
            :name,
            :email,
            :mobile,
            :enrollment_number,
            :course_id,
            :semester,
            :password_hash
        )";

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


// ==========================================
// SUCCESS
// ==========================================

echo "<!DOCTYPE html>";
echo "<html lang='en'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<title>Student Added</title>";
echo "</head>";
echo "<body>";

echo "<h2>Student Added Successfully!</h2>";

echo "<p>";
echo "Student <strong>" .
     htmlspecialchars($name) .
     "</strong> has been added successfully.";
echo "</p>";

echo "<a href='../pages/admin-add-student.html'>";
echo "Add Another Student";
echo "</a>";

echo "<br><br>";

echo "<a href='../pages/admin.html'>";
echo "Back to Admin Dashboard";
echo "</a>";

echo "</body>";
echo "</html>";

?>