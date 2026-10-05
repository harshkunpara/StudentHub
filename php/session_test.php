<?php
session_start();

echo "<h2>StudentHub Session Test</h2>";

echo "<pre>";
print_r($_SESSION);
echo "</pre>";

if (isset($_SESSION['student_id'])) {

    echo "<h3 style='color:green'>SESSION WORKING</h3>";

    echo "<p>Login successful. You can go to Dashboard.</p>";

    echo "
        <a href='../pages/dashboard.html'
           style='
           display:inline-block;
           padding:12px 20px;
           background:#198754;
           color:white;
           text-decoration:none;
           border-radius:6px;
           font-weight:bold;
           '>
           Go to Dashboard
        </a>
    ";

} else {

    echo "<h3 style='color:red'>NO STUDENT SESSION</h3>";

    echo "
        <a href='../pages/login.html'
           style='
           display:inline-block;
           padding:12px 20px;
           background:#dc3545;
           color:white;
           text-decoration:none;
           border-radius:6px;
           font-weight:bold;
           '>
           Go to Login
        </a>
    ";
}
?>