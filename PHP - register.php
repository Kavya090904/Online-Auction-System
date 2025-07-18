<?php
$SN = "localhost";
$UN = "root";
$p = "";
$db = "eauctiondb";

$conn = new mysqli($SN, $UN, $p, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve form data
$username = $_POST['username'];
$password = $_POST['password'];
$email = $_POST['email'];
$phoneNumber = $_POST['phoneNumber'];
$address = $_POST['address'];

// Insert user data into the database
$sql = "INSERT INTO User (Username, Password, Email, PhoneNumber, Address)
        VALUES ('$username', '$password', '$email', '$phoneNumber', '$address')";

if ($conn->query($sql) === TRUE) {
    echo "Registration successful!";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>