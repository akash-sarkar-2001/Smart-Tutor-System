<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sts";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rollno = $_POST['rollno'];
// Create connection
$con = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
}
?>