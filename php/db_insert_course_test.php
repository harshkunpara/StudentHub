<?php
require __DIR__ . "/db_connect.php";
$message = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $mobile = trim($_POST["mobile"] ?? "");
    $enrollment = trim($_POST["enrollment_number"] ?? "");
    $courseId = (int)($_POST["course_id"] ?? 0);
    $semester = (int)($_POST["semester"] ?? 0);
    if ($name && filter_var($email,FILTER_VALIDATE_EMAIL) && $mobile && $enrollment && $courseId && $semester >= 1 && $semester <= 8) {
        $stmt=$pdo->prepare("INSERT INTO students (name,email,mobile,enrollment_number,course_id,semester,password_hash) VALUES (:name,:email,:mobile,:enrollment,:course_id,:semester,:password_hash)");
        $stmt->execute([":name"=>$name,":email"=>$email,":mobile"=>$mobile,":enrollment"=>$enrollment,":course_id"=>$courseId,":semester"=>$semester,":password_hash"=>password_hash("Student@123",PASSWORD_DEFAULT)]);
        $message="Student inserted successfully with department relation!";
    } else $message="Please provide valid details.";
}
$courses=$pdo->query("SELECT course_id,course_name FROM courses ORDER BY course_name")->fetchAll();
?>
<!doctype html><html><head><meta charset="UTF-8"><title>Insert Student with Department</title></head><body>
<h2>StudentHub - Insert Student with Department</h2><?php if($message) echo '<p>'.htmlspecialchars($message).'</p>'; ?>
<form method="POST"><input name="name" placeholder="Full name" required><br><br><input type="email" name="email" placeholder="Email" required><br><br><input name="mobile" placeholder="Mobile" required><br><br><input name="enrollment_number" placeholder="Enrollment number" required><br><br>
<select name="course_id" required><option value="">Select department</option><?php foreach($courses as $c): ?><option value="<?= (int)$c['course_id'] ?>"><?= htmlspecialchars($c['course_name']) ?></option><?php endforeach; ?></select><br><br>
<select name="semester" required><option value="">Select semester</option><?php for($i=1;$i<=8;$i++): ?><option value="<?= $i ?>">Semester <?= $i ?></option><?php endfor; ?></select><br><br><button>Save Student</button></form>
</body></html>
