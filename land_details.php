<?php
session_start();

// Function to get user ID from database based on username
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

$userID = null; // Initialize userID variable
$loggedIn = false; // Initialize loggedIn variable

// Check if user is logged in
if (isset($_SESSION['username'])) {
    // Get userID using getUserId function
    $userID = getUserId($conn, $_SESSION['username']);
    $loggedIn = true; // User is logged in
}

// Function to fetch land details from database
function getLandDetails($conn, $landID)
{
    $stmt = $conn->prepare("SELECT l.*, u.username AS OwnerFullName, u.Email AS OwnerEmail, u.PhoneNumber AS OwnerContact FROM Land l INNER JOIN User u ON l.OwnerID = u.UserID WHERE LandID = ?");
    $stmt->bind_param("i", $landID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        return $result->fetch_assoc();
    } else {
        return false;
    }
}

// Function to check if the user is interested in the land
function isInterested($conn, $landID, $userID)
{
    $stmt = $conn->prepare("SELECT * FROM Interest WHERE LandID = ? AND UserID = ?");
    $stmt->bind_param("ii", $landID, $userID);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->num_rows > 0;
}

// Get land ID from query string
if (isset($_GET['land_id'])) {
    $landID = $_GET['land_id'];

    // Fetch land details
    $land = getLandDetails($conn, $landID);

    // Check if the user is interested in this land (if logged in)
    if ($loggedIn) {
        $interested = isInterested($conn, $landID, $userID);
    }
} else {
    // Redirect to dashboard if land ID is not provided
    header("Location: dashboard.php");
    exit;
}

$conn->close(); // Close database connection
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Land Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <?php include('PHP - menu.php'); ?>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <h2>
                    <?php echo $land['Title']; ?>
                </h2>
                <!-- Image carousel -->
                <div id="imageCarousel" class="carousel slide" data-ride="carousel">
                    <div class="carousel-inner">
                        <?php
                        // Fetch and display images
                        $images = explode(', ', $land['Images']);
                        foreach ($images as $key => $image) {
                            $active = ($key === 0) ? 'active' : '';
                            echo '<div class="carousel-item ' . $active . '">';
                            echo '<img class="d-block w-100" src="' . $image . '" alt="Image ' . ($key + 1) . '">';
                            echo '</div>';
                        }
                        ?>
                    </div>
                    <!-- Previous and Next buttons -->
                    <a class="carousel-control-prev" href="#imageCarousel" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#imageCarousel" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
                <p><strong>Description:</strong>
                    <?php echo $land['Description']; ?>
                </p>
                <p><strong>Address:</strong>
                    <?php echo $land['Address']; ?>
                </p>
                <p><strong>Plot Area:</strong>
                    <?php echo $land['PlotArea']; ?>
                </p>
                <p><strong>Dimension:</strong>
                    <?php echo $land['Dimension']; ?>
                </p>
                <p><strong>Starting Bid:</strong>
                    <?php echo $land['StartingBid']; ?>
                </p>
                <?php if ($loggedIn && !$interested): ?>
                    <form action="process_interest.php" method="post">
                        <input type="hidden" name="land_id" value="<?php echo $land['LandID']; ?>">
                        <button type="submit" class="btn btn-primary">Show Interest</button>
                    </form>
                <?php elseif ($loggedIn && $interested): ?>
                    <p>You've already shown interest in this land.</p>
                <?php else: ?>
                    <p><a href="login.php">Login</a> to show interest.</p>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <h2>Owner Details</h2>
                <?php if ($land): ?>
                    <p><strong>Name:</strong>
                        <?php echo $land['OwnerFullName']; ?>
                    </p>
                    <p><strong>Email:</strong>
                        <?php echo $land['OwnerEmail']; ?>
                    </p>
                    <?php if ($loggedIn): ?>
                        <p><strong>Contact:</strong>
                            <?php echo $land['OwnerContact']; ?>
                        </p>
                    <?php else: ?>
                        <p><em>Login to view contact details.</em></p>
                    <?php endif; ?>
                <?php else: ?>
                    <p>Owner details not found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>

</html>