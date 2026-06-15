<?php
// Replace these values with your actual InfinityFree MySQL Details
$host = "sql105.infinityfree.com"; // <-- Paste your exact MySQL Hostname here
$user = "if0_42138223";           // <-- Paste your exact MySQL Username here
$pass = "inventory09";   // <-- Paste your exact MySQL Password here
$dbname = "if0_42138223_inventory"; // <-- Paste your full Database Name here

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>