<?php
session_start();

function getUserId($conn, $username)
{
    $stmt = $conn->prepare("SELECT UserID FROM User WHERE Username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['UserID'];
    } else {
        return null;
    }
}

$SN = "localhost";
$UN = "root";
$p = "";
$db = "eauctiondb";

$conn = new mysqli($SN, $UN, $p, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['username'])) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header("Location: login.php");
    exit;
} else {
    $userID = getUserId($conn, $_SESSION['username']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['land_id'])) {
    $landID = $_POST['land_id'];

    $userID = getUserIdFromSession();

    if (!$userID) {
        header("Location: HTML - login.html");
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO Interest (LandID, UserID) VALUES (?, ?)");
    $stmt->bind_param("ii", $landID, $userID);

    if ($stmt->execute()) {
        header("Location: land_details.php?land_id=" . $landID . "&success=true");
        exit;
    } else {
        header("Location: land_details.php?land_id=" . $landID . "&error=true");
        exit;
    }
} else {
    header("Location: dashboard.php");
    exit;
}

function getUserIdFromSession() {
    return isset($_SESSION['userID']) ? $_SESSION['userID'] : null;
}