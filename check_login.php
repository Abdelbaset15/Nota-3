<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login_signup.php"); 
    exit();
}
?>
