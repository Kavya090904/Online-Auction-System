<?php
$SN = "localhost";
$UN = "root";
$p = "";
$db = "eauctiondb";

$conn = new mysqli($SN, $UN, $p, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM User WHERE Username = '$username' AND Password = '$password'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    session_start();
    $_SESSION['username'] = $username;
    header("Location: index.php"); 
} else {
    echo "Invalid username or password";
}

$conn->close();

