<?php
// Practical 7 - Read registrations.csv and display records
$csvFile = "../data/registrations.csv";

if (!file_exists($csvFile)) {
    die("No registration records found.");
}

$file = fopen($csvFile, "r");

if ($file === false) {
    die("Unable to open registration file.");
}

$students = [];
$header = fgetcsv($file);

while (($row = fgetcsv($file)) !== false) {
    if (count($row) >= 8) {
        $students[] = $row;
    }
}

fclose($file);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>StudentHub - Registered Students</title>
<style>
body{font-family:Arial,sans-serif;background:#f5f7fb;margin:0;padding:30px}
.container{max-width:1400px;margin:auto}
.card{background:#fff;border-radius:16px;padding:25px;box-shadow:0 10px 30px rgba(0,0,0,.08)}
h1{margin-top:0}
.count{color:#666;margin-bottom:18px}
.table-wrap{overflow:auto}
table{width:100%;border-collapse:collapse;min-width:1050px}
th,td{border:1px solid #ddd;padding:11px;text-align:left}
th{background:#20243a;color:#fff}
tr:nth-child(even){background:#f8f9fc}
.btn{display:inline-block;margin-top:18px;padding:10px 15px;background:#4f46e5;color:#fff;text-decoration:none;border-radius:8px}
.badge{display:inline-block;padding:5px 9px;border-radius:20px;background:#e8f5e9}
</style>
</head>
<body>
<div class="container">
<div class="card">
<h1>Registered Students</h1>
<div class="count">Total registrations: <?= count($students) ?></div>

<?php if (count($students) === 0): ?>
    <p>No student registrations yet.</p>
<?php else: ?>
<div class="table-wrap">
<table>
<thead>
<tr>
<th>#</th>
<th>Name</th>
<th>Email</th>
<th>Phone</th>
<th>Enrollment</th>
<th>Department</th>
<th>Semester</th>
<th>Gender</th>
</tr>
</thead>
<tbody>
<?php foreach ($students as $index => $row): ?>
<tr>
<td><?= $index + 1 ?></td>
<td><?= htmlspecialchars($row[0], ENT_QUOTES, "UTF-8") ?></td>
<td><?= htmlspecialchars($row[1], ENT_QUOTES, "UTF-8") ?></td>
<td><?= htmlspecialchars($row[2], ENT_QUOTES, "UTF-8") ?></td>
<td><?= htmlspecialchars($row[3], ENT_QUOTES, "UTF-8") ?></td>
<td><?= htmlspecialchars($row[4], ENT_QUOTES, "UTF-8") ?></td>
<td><?= htmlspecialchars($row[5], ENT_QUOTES, "UTF-8") ?></td>
<td><span class="badge"><?= htmlspecialchars($row[6], ENT_QUOTES, "UTF-8") ?></span></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php endif; ?>

<a class="btn" href="../pages/registar.html">Back to Registration</a>
</div>
</div>
</body>
</html>
