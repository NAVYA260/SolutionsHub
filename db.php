<?php
$servername = "localhost";
$username = "root"; // Default MySQL username
$password = "1234"; // Default MySQL password (empty by default in XAMPP)
$dbname = "solutionhub";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
