<?php
require __DIR__ . "/db_connect.php";
$message = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int)($_POST["id"] ?? 0);
    $email = trim($_POST["email"] ?? "");
    if ($id > 0 && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $pdo->prepare("UPDATE students SET email = :email WHERE id = :id");
        $stmt->execute([":email"=>$email,":id"=>$id]);
        $message = $stmt->rowCount() ? "Student email updated successfully!" : "No student record was changed.";
    } else $message = "Enter a valid student ID and email.";
}
?>
<!doctype html><html><head><meta charset="UTF-8"><title>Update Student</title></head><body>
<h2>Update Student Email</h2><?php if($message) echo '<p>'.htmlspecialchars($message).'</p>'; ?>
<form method="POST"><input type="number" name="id" min="1" placeholder="Student ID" required><br><br><input type="email" name="email" placeholder="New email" required><br><br><button>Update</button></form>
</body></html>
