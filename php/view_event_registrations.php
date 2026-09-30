<?php
require __DIR__ . "/db_connect.php";
$sql="SELECT r.reg_id,s.name,s.enrollment_number,e.title,e.event_date,e.location,r.registered_on FROM registrations r INNER JOIN students s ON r.student_id=s.id INNER JOIN events e ON r.event_id=e.event_id ORDER BY r.registered_on DESC";
$rows=$pdo->query($sql)->fetchAll();
?>
<!doctype html><html><head><meta charset="UTF-8"><title>Event Registrations</title></head><body><h2>Event Registrations</h2><table border="1" cellpadding="8"><tr><th>Registration</th><th>Student</th><th>Enrollment</th><th>Event</th><th>Date</th><th>Location</th><th>Registered On</th></tr><?php foreach($rows as $r): ?><tr><td><?= (int)$r['reg_id'] ?></td><td><?= htmlspecialchars($r['name']) ?></td><td><?= htmlspecialchars($r['enrollment_number']) ?></td><td><?= htmlspecialchars($r['title']) ?></td><td><?= htmlspecialchars($r['event_date']) ?></td><td><?= htmlspecialchars($r['location']) ?></td><td><?= htmlspecialchars($r['registered_on']) ?></td></tr><?php endforeach; ?></table></body></html>
