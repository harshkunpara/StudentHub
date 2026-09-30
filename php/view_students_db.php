<?php
require __DIR__ . "/db_connect.php";
$sql = "SELECT s.id,s.name,s.email,s.mobile,s.enrollment_number,c.course_name,s.semester,s.gender,s.city,s.created_at FROM students s INNER JOIN courses c ON s.course_id=c.course_id ORDER BY s.id DESC";
$students=$pdo->query($sql)->fetchAll();
?>
<!doctype html><html><head><meta charset="UTF-8"><title>StudentHub - Registered Students</title></head><body>
<h1>Registered Students</h1>
<table border="1" cellpadding="8"><tr><th>ID</th><th>Name</th><th>Email</th><th>Mobile</th><th>Enrollment</th><th>Department</th><th>Semester</th><th>Gender</th><th>City</th><th>Created</th><th>Actions</th></tr>
<?php foreach($students as $s): ?><tr><td><?= (int)$s['id'] ?></td><td><?= htmlspecialchars($s['name']) ?></td><td><?= htmlspecialchars($s['email']) ?></td><td><?= htmlspecialchars($s['mobile']) ?></td><td><?= htmlspecialchars($s['enrollment_number']) ?></td><td><?= htmlspecialchars($s['course_name']) ?></td><td><?= (int)$s['semester'] ?></td><td><?= htmlspecialchars($s['gender'] ?? '') ?></td><td><?= htmlspecialchars($s['city'] ?? '') ?></td><td><?= htmlspecialchars($s['created_at']) ?></td><td><a href="update_student_db.php?id=<?= (int)$s['id'] ?>">Update</a> | <a href="delete_student_db.php?id=<?= (int)$s['id'] ?>">Delete</a></td></tr><?php endforeach; ?></table>
</body></html>
