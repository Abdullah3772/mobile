<?php
// config/db.php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "mobile_shop_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Global configuration variables for the shop
$shop_name = "Abdullah Mobile World";
$shop_address = "No. 53, Satham Hussain Road, Eravur, Sri Lanka";
$shop_phone = "+94759817361";
?>