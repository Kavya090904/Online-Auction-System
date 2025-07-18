<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <!-- Bootstrap CSS -->
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
                <h2 class="card-title mb-4">Edit Profile</h2>
                <?php
                // Start the session
                session_start();

                // Check if user is logged in
                if(isset($_SESSION['username'])) {
                    $SN = "localhost";
                    $UN = "root";
                    $p = "";
                    $db = "eauctiondb";
                    
                    $conn = new mysqli($SN, $UN, $p, $db);

                    // Check connection
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    // Get user details from database
                    $username = $_SESSION['username'];
                    $sql = "SELECT * FROM User WHERE Username='$username'";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        // Display user details in form for editing
                        $row = $result->fetch_assoc();
                ?>
                <form action="update_profile.php" method="post">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo $row['Username']; ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $row['Email']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phoneNumber">Phone Number:</label>
                        <input type="text" class="form-control" id="phoneNumber" name="phoneNumber" value="<?php echo $row['PhoneNumber']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Address:</label>
                        <textarea class="form-control" id="address" name="address" required><?php echo $row['Address']; ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
                <?php
                    } else {
                        echo "<p>User not found.</p>";
                    }

                    $conn->close();
                } else {
                    echo "<p>Please log in to edit your profile.</p>";
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
