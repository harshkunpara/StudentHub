<?php
session_start();

$_SESSION["student_id"] = 1;
$_SESSION["student_name"] = "Harsh Test";

echo "<h2>Session Write Test</h2>";

echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<p>Now open session_read_test.php</p>";
?>