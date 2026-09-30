<?php
session_start();

echo "<h2>Session Read Test</h2>";

echo "<pre>";
print_r($_SESSION);
echo "</pre>";

if (isset($_SESSION["student_id"])) {
    echo "<h2 style='color:green'>SESSION WORKING ✅</h2>";
} else {
    echo "<h2 style='color:red'>SESSION NOT WORKING ❌</h2>";
}
?>