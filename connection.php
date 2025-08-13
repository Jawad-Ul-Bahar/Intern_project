<?php
$con = mysqli_connect("localhost", "root", "", "adminpanel"); // replace with your DB name

if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
