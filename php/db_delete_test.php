<?php
require __DIR__ . "/db_connect.php";
$message = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int)($_POST["id"] ?? 0);
    if ($id > 0) {
        $stmt = $pdo->prepare("DELETE FROM students WHERE id = :id");
        $stmt->execute([":id"=>$id]);
        $message = $stmt->rowCount() ? "Student record deleted successfully!" : "Student ID not found.";
    }
}
?>
<!doctype html><html><head><meta charset="UTF-8"><title>Delete Student</title></head><body>
<h2>Delete Student</h2><?php if($message) echo '<p>'.htmlspecialchars($message).'</p>'; ?>
<form method="POST" onsubmit="return confirm('Delete this student?');"><input type="number" name="id" min="1" placeholder="Student ID" required><br><br><button type="submit">Delete Student</button></form>
</body></html>
