<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli('localhost', 'root', 'Ahmed123@', 'nota');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
