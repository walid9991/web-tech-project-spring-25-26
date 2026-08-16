<?php
// Database credentials
$host = "localhost";
$db_name = "p03"; // Shared database
$username = "root";
$password = "";

$mysqli = new mysqli($host, $username, $password, $db_name);
if ($mysqli->connect_errno) {
    die("Connection failed: " . $mysqli->connect_error);
}

$mysqli->set_charset("utf8mb4");