<?php
session_start();
unset($_SESSION["user_id"]);
unset($_SESSION["username"]);
session_destroy(); // Optionally destroy the entire session
header("Location: login.php");
?>
