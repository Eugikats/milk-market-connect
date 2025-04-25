<?php
// config/db.php
$host = "sql210.infinityfree.com";
$username = "if0_38761393";
$password = "NQR1m0ZF7k7eN";
$database = "if0_38761393_milk_market";

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>