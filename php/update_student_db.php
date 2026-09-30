<?php
require __DIR__ . "/db_connect.php";
$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) die("Provide a valid student ID, for example ?id=1");
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    if (!filter_var($email,FILTER_VALIDATE_EMAIL)) die("Invalid email.");
    $stmt=$pdo->prepare("UPDATE students SET email=:email WHERE id=:id");
    $stmt->execute([":email"=>$email,":id"=>$id]);
    echo "Student email updated successfully! <a href='view_students_db.php'>View students</a>";
    exit;
}
?>
<!doctype html><html><head><meta charset="UTF-8"><title>Update Student</title></head><body><h2>Update Student <?= $id ?></h2><form method="POST"><input type="email" name="email" placeholder="New email" required><button>Update</button></form></body></html>
