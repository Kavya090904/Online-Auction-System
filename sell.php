<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Redirect to login page if user is not logged in
if (!isset($_SESSION['username'])) {
    header("Location: HTML - login.html");
    exit;
}

require_once 'helper/session_handler.php';
require_once 'helper/db_connection.php';
require_once 'helper/land_functions.php';

$message = "";
$messageClass = "";
$userID = null;

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $userID = getUserIdFromSession();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $landID = $_POST['delete'];
    if (deleteLand($conn, $landID)) {
        $message = "<p class='text-success'>Land deleted successfully!</p>";
        $messageClass = "alert-success";
    } else {
        $message = "<p class='text-danger'>Error deleting land.</p>";
        $messageClass = "alert-danger";
    }
}

$lands = [];
if ($userID) {
    $lands = getLandsByUser($conn, $userID);
}

$insertMessage = insertLandDetails($conn);
if ($insertMessage) {
    $message = $insertMessage["message"];
    $messageClass = $insertMessage["class"];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sell Land</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include('partials/menu.php'); ?>

    <div class="container mt-5">
        <h2>Sell Land</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"
            enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" class="form-control" id="address" name="address" required>
            </div>
            <div class="form-group">
                <label for="plotArea">Plot Area:</label>
                <input type="number" class="form-control" id="plotArea" name="plotArea" required>
            </div>
            <div class="form-group">
                <label for="dimension">Dimension:</label>
                <input type="text" class="form-control" id="dimension" name="dimension" required>
            </div>
            <div class="form-group">
                <label for="startingBid">Starting Bid:</label>
                <input type="number" class="form-control" id="startingBid" name="startingBid" required>
            </div>
            <div class="form-group">
                <label for="images">Images:</label>
                <input type="file" class="form-control-file" id="images" name="images[]" multiple accept="image/*">
            </div>
            <div class="form-group">
                <label for="gif">GIF:</label>
                <input type="file" class="form-control-file" id="gif" name="gif" accept="image/gif">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form><br>
        <?php if ($message): ?>
            <div class="alert <?php echo $messageClass; ?>" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="container mt-5">
        <h2>My Lands</h2>
        <div class="row">
            <?php if (!empty($lands)): ?>
                <?php foreach ($lands as $land): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <?php
                            $images = explode(',', $land['Images']);
                            $firstImage = reset($images);
                            ?>
                            <img src="<?php echo $firstImage; ?>" class="card-img-top" alt="Land Image">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <?php echo $land['Title']; ?>
                                </h5>
                                <p class="card-text">
                                    <?php echo $land['Description']; ?>
                                </p>
                                <p class="card-text"><strong>Address:</strong>
                                    <?php echo $land['Address']; ?>
                                </p>
                                <p class="card-text"><strong>Plot Area:</strong>
                                    <?php echo $land['PlotArea']; ?>
                                </p>
                                <p class="card-text"><strong>Dimension:</strong>
                                    <?php echo $land['Dimension']; ?>
                                </p>
                                <p class="card-text"><strong>Starting Bid:</strong>
                                    <?php echo $land['StartingBid']; ?>
                                </p>
                                <form class="d-inline" method="post" action="update.php">
                                    <input type="hidden" name="land_id" value="<?php echo $land['LandID']; ?>">
                                    <button type="submit" class="btn btn-primary btn-sm ml-2">Update</button>
                                </form>
                                <form class="d-inline" method="post" action="">
                                    <input type="hidden" name="delete" value="<?php echo $land['LandID']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to delete this land?')">Delete</button>
                                </form>

                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-md-12">
                    <p>No lands added yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>