<?php
// StudentHub Admin page - reads the same CSV used by Practical 7
$csvFile = "../data/registrations.csv";
$students = [];

if (file_exists($csvFile)) {
    $file = fopen($csvFile, "r");

    if ($file !== false) {
        fgetcsv($file); // skip header

        while (($row = fgetcsv($file)) !== false) {
            if (count($row) >= 8) {
                $students[] = $row;
            }
        }

        fclose($file);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>StudentHub Admin</title>
<style>
body{margin:0;font-family:Arial,sans-serif;background:#f4f6fb;color:#20243a}
header{background:#20243a;color:white;padding:22px 30px}
main{max-width:1450px;margin:30px auto;padding:0 20px}
.stats{display:flex;gap:18px;flex-wrap:wrap}
.stat{background:white;padding:20px 25px;border-radius:15px;box-shadow:0 8px 25px rgba(0,0,0,.07);min-width:180px}
.stat strong{font-size:28px;display:block}
.card{background:white;margin-top:25px;padding:25px;border-radius:15px;box-shadow:0 8px 25px rgba(0,0,0,.07)}
.table-wrap{overflow:auto}
table{width:100%;border-collapse:collapse;min-width:1050px}
th,td{padding:11px;border-bottom:1px solid #ddd;text-align:left}
th{background:#20243a;color:white}
tr:hover{background:#f7f8fc}
.note{color:#666}
a{color:#4f46e5}
</style>
</head>
<body>
<header><h1>StudentHub Admin Console</h1></header>
<main>
<div class="stats">
<div class="stat"><span>Total Students</span><strong><?= count($students) ?></strong></div>
<div class="stat"><span>Storage</span><strong>CSV</strong></div>
<div class="stat"><span>Passwords</span><strong>Hashed</strong></div>
</div>

<div class="card">
<h2>All Registered Students</h2>
<p class="note">Password hashes are intentionally not displayed in this admin table.</p>

<?php if (!$students): ?>
<p>No registrations found. Register a student first.</p>
<?php else: ?>
<div class="table-wrap">
<table>
<thead>
<tr>
<th>#</th><th>Name</th><th>Email</th><th>Phone</th>
<th>Enrollment</th><th>Department</th><th>Semester</th><th>Gender</th>
</tr>
</thead>
<tbody>
<?php foreach ($students as $i => $row): ?>
<tr>
<td><?= $i + 1 ?></td>
<td><?= htmlspecialchars($row[0], ENT_QUOTES, "UTF-8") ?></td>
<td><?= htmlspecialchars($row[1], ENT_QUOTES, "UTF-8") ?></td>
<td><?= htmlspecialchars($row[2], ENT_QUOTES, "UTF-8") ?></td>
<td><?= htmlspecialchars($row[3], ENT_QUOTES, "UTF-8") ?></td>
<td><?= htmlspecialchars($row[4], ENT_QUOTES, "UTF-8") ?></td>
<td><?= htmlspecialchars($row[5], ENT_QUOTES, "UTF-8") ?></td>
<td><?= htmlspecialchars($row[6], ENT_QUOTES, "UTF-8") ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php endif; ?>
</div>

<p><a href="view_registrations.php">Open Practical 7 records page</a></p>
</main>
</body>
</html>
