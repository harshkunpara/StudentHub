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


/* =========================
   NAME VALIDATION
   ========================= */

if ($name === "") {

    $errors[] = "Full name is required.";

} elseif (!preg_match('/^[A-Za-z ]{2,50}$/', $name)) {

    $errors[] = "Name must contain only letters and spaces.";

}


/* =========================
   EMAIL VALIDATION
   ========================= */

if ($email === "") {

    $errors[] = "Email is required.";

} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    $errors[] = "Enter a valid email address.";

}


/* =========================
   MOBILE VALIDATION
   ========================= */

if ($mobile === "") {

    $errors[] = "Mobile number is required.";

} elseif (!preg_match('/^[0-9]{10}$/', $mobile)) {

    $errors[] = "Mobile number must contain exactly 10 digits.";

}


/* =========================
   ENROLLMENT VALIDATION
   ========================= */

if ($enrollment === "") {

    $errors[] = "Enrollment number is required.";

} elseif (!preg_match('/^[0-9]{2}IT[0-9]{3}$/i', $enrollment)) {

    $errors[] = "Enter a valid enrollment number. Example: 25IT048.";

}


/* =========================
   DEPARTMENT VALIDATION
   ========================= */

if ($department === "") {

    $errors[] = "Department is required.";

}


/* =========================
   SEMESTER VALIDATION
   ========================= */

if (!preg_match('/^Semester [1-8]$/', $semesterText)) {

    $errors[] = "Select a valid semester.";

}

$semester = (int) str_replace("Semester ", "", $semesterText);


/* =========================
   PASSWORD VALIDATION
   ========================= */

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


/* =========================
   CONFIRM PASSWORD
   ========================= */

if ($password !== $confirmPassword) {

    $errors[] = "Passwords do not match.";

}


/* =========================
   CHECK DEPARTMENT IN DB
   ========================= */

if (!$errors) {

    $courseStmt = $pdo->prepare(
        "SELECT course_id
         FROM courses
         WHERE course_name = :course_name"
    );

    $courseStmt->execute([
        ":course_name" => $department
    ]);

    $courseId = $courseStmt->fetchColumn();

    if (!$courseId) {

        $errors[] = "Selected department is not available in the database.";

    }
}


/* =========================
   CHECK DUPLICATE EMAIL
   OR ENROLLMENT
   ========================= */

if (!$errors) {

    $check = $pdo->prepare(
        "SELECT id
         FROM students
         WHERE email = :email
            OR enrollment_number = :enrollment
         LIMIT 1"
    );

    $check->execute([
        ":email" => $email,
        ":enrollment" => $enrollment
    ]);

    if ($check->fetch()) {

        $errors[] = "Email or enrollment number is already registered.";

    }
}


/* =========================
   SHOW ERRORS
   ========================= */

if ($errors) {

    header(
        "Location: ../pages/registar.html?errors=" .
        rawurlencode(json_encode($errors))
    );

    exit;
}


/* =========================
   PASSWORD HASH
   ========================= */

$passwordHash = password_hash(
    $password,
    PASSWORD_DEFAULT
);


/* =========================
   INSERT STUDENT
   ========================= */

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


/* =========================
   SUCCESS
   ========================= */

header("Location: registration_success.php");
exit;

?>