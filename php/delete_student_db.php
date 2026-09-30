<?php
require __DIR__ . "/db_connect.php";
$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) die("Provide a valid student ID, for example ?id=1");
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $stmt=$pdo->prepare("DELETE FROM students WHERE id=:id");
    $stmt->execute([":id"=>$id]);
    echo $stmt->rowCount() ? "Student deleted successfully!" : "Student ID not found.";
    echo " <a href='view_students_db.php'>View students</a>";
    exit;
}
?>
<!doctype html><html><head><meta charset="UTF-8"><title>Delete Student</title></head><body><h2>Delete Student <?= $id ?>?</h2><form method="POST"><button type="submit">Confirm Delete</button></form></body></html>
