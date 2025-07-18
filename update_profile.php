<?php
// Start the session
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    // If not logged in, redirect to login page
    header("Location: HTML - login.html");
    exit;
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $newEmail = $_POST["email"];
    $newPhoneNumber = $_POST["phoneNumber"];
    $newAddress = $_POST["address"];

    // Validate form data (you can add more validation as needed)
    if (empty($newEmail) || empty($newPhoneNumber) || empty($newAddress)) {
        // Redirect back to edit profile page with error message
        header("Location: edit_profile.php?error=emptyfields");
        exit;
    } else {
        $SN = "localhost";
        $UN = "root";
        $p = "";
        $db = "eauctiondb";

        $conn = new mysqli($SN, $UN, $p, $db);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Prepare SQL statement to update user's profile
        $username = $_SESSION['username'];
        $sql = "UPDATE User SET Email='$newEmail', PhoneNumber='$newPhoneNumber', Address='$newAddress' WHERE Username='$username'";

        if ($conn->query($sql) === TRUE) {
            // Profile updated successfully
            // Redirect to profile page with success message
            header("Location: profile.php?update=success");
            exit;
        } else {
            // Error updating profile
            // Redirect to edit profile page with error message
            header("Location: edit_profile.php?error=sqlerror");
            exit;
        }
    }
} else {
    // If accessed directly without form submission, redirect to edit profile page
    header("Location: edit_profile.php");
    exit;
}
?>