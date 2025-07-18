<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-body">
                <h2 class="card-title mb-4">User Profile</h2>
                <?php
                session_start();
                if (isset($_SESSION['username'])) {
                    $SN = "localhost";
                    $UN = "root";
                    $p = "";
                    $db = "eauctiondb";

                    $conn = new mysqli($SN, $UN, $p, $db);
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    $username = $_SESSION['username'];
                    $sql = "SELECT * FROM User WHERE Username='$username'";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<p class='lead'>Username: " . $row["Username"] . "</p>";
                            echo "<p class='lead'>Email: " . $row["Email"] . "</p>";
                            echo "<p class='lead'>Phone Number: " . $row["PhoneNumber"] . "</p>";
                            echo "<p class='lead'>Address: " . $row["Address"] . "</p>";
                        }
                    } else {
                        echo "<p>User not found.</p>";
                    }

                    $conn->close();
                } else {
                    echo "<p>Please log in to view your profile.</p>";
                }
                ?>
                <div class="mt-4">
                    <a href="dashboard.php" class="btn btn-outline-primary mr-2">Back</a>
                    <a href="edit_profile.php" class="btn btn-primary btn-lg">Edit Profile</a>
                    <a href="logout.php" class="btn btn-danger btn-lg ml-2">Logout</a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>