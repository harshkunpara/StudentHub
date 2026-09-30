<?php
require __DIR__ . "/db_connect.php";
$sql = "SELECT s.id,s.name,s.email,s.mobile,s.enrollment_number,c.course_name,s.semester,s.created_at FROM students s INNER JOIN courses c ON s.course_id=c.course_id ORDER BY s.id";
$students = $pdo->query($sql)->fetchAll();
?>
<!doctype html><html><head><meta charset="UTF-8"><title>StudentHub JOIN Test</title></head><body>
<h2>Students with Department (JOIN)</h2>
<table border="1" cellpadding="8"><tr><th>ID</th><th>Name</th><th>Enrollment</th><th>Email</th><th>Department</th><th>Semester</th></tr>
<?php foreach($students as $s): ?><tr><td><?= (int)$s['id'] ?></td><td><?= htmlspecialchars($s['name']) ?></td><td><?= htmlspecialchars($s['enrollment_number']) ?></td><td><?= htmlspecialchars($s['email']) ?></td><td><?= htmlspecialchars($s['course_name']) ?></td><td><?= (int)$s['semester'] ?></td></tr><?php endforeach; ?></table>
</body></html>
