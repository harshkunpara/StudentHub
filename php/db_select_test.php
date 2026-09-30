<?php
require __DIR__ . "/db_connect.php";
$students = $pdo->query("SELECT s.id,s.name,s.email,s.mobile,s.enrollment_number,c.course_name,s.semester,s.created_at FROM students s INNER JOIN courses c ON s.course_id=c.course_id ORDER BY s.id DESC")->fetchAll();
?>
<!doctype html><html><head><meta charset="UTF-8"><title>StudentHub Students</title></head><body>
<h2>Registered Students</h2>
<table border="1" cellpadding="8"><tr><th>ID</th><th>Name</th><th>Email</th><th>Mobile</th><th>Enrollment</th><th>Department</th><th>Semester</th><th>Created</th></tr>
<?php foreach($students as $s): ?><tr><td><?= (int)$s['id'] ?></td><td><?= htmlspecialchars($s['name']) ?></td><td><?= htmlspecialchars($s['email']) ?></td><td><?= htmlspecialchars($s['mobile']) ?></td><td><?= htmlspecialchars($s['enrollment_number']) ?></td><td><?= htmlspecialchars($s['course_name']) ?></td><td><?= (int)$s['semester'] ?></td><td><?= htmlspecialchars($s['created_at']) ?></td></tr><?php endforeach; ?>
</table></body></html>
