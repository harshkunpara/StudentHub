<?php

session_start();

unset($_SESSION["admin_logged_in"]);
unset($_SESSION["admin_username"]);

session_regenerate_id(true);

header("Location: ../pages/admin-login.html");
exit;

?>