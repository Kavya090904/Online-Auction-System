<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: HTML - login.html");
    exit;
}

$SN = "localhost";
$UN = "root";
$p = "";
$db = "eauctiondb";

$conn = new mysqli($SN, $UN, $p, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function getLandById($conn, $landID)
{
    $stmt = $conn->prepare("SELECT * FROM Land WHERE LandID = ?");
    $stmt->bind_param("i", $landID);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}

$landID = isset($_POST['land_id']) ? $_POST['land_id'] : (isset($_GET['land_id']) ? $_GET['land_id'] : null);

if (!$landID) {
    die("Land ID not provided.");
}

$land = getLandById($conn, $landID);

if (!$land) {
    die("Land not found.");
}

// Fetch existing images and GIF
$images = explode(",", $land['Images']); // Assuming images are stored as comma-separated URLs in the database
$gif = $land['GIF'];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Land</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include('PHP - menu.php'); ?>

    <div class="container mt-5">
        <h2>Update Land Details</h2>
        <form action="process_update.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="land_id" value="<?php echo $land['LandID']; ?>">
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo $land['Title']; ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea class="form-control" id="description" name="description" rows="3" required><?php echo $land['Description']; ?></textarea>
            </div>
            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" class="form-control" id="address" name="address" value="<?php echo $land['Address']; ?>" required>
            </div>
            <div class="form-group">
                <label for="plotArea">Plot Area:</label>
                <input type="number" class="form-control" id="plotArea" name="plotArea" value="<?php echo $land['PlotArea']; ?>" required>
            </div>
            <div class="form-group">
                <label for="dimension">Dimension:</label>
                <input type="text" class="form-control" id="dimension" name="dimension" value="<?php echo $land['Dimension']; ?>" required>
            </div>
            <div class="form-group">
                <label for="startingBid">Starting Bid:</label>
                <input type="number" class="form-control" id="startingBid" name="startingBid" value="<?php echo $land['StartingBid']; ?>" required>
            </div>
            <!-- Display existing images -->
            <div class="form-group">
                <label for="existing_images">Existing Images:</label>
                <div id="existing_images">
                    <?php foreach ($images as $image) : ?>
                        <img src="<?php echo $image; ?>" alt="Land Image" style="width: 150px; height: 150px; margin-right: 10px;">
                    <?php endforeach; ?>
                </div>
            </div>
            <!-- Display existing GIF -->
            <div class="form-group">
                <label for="existing_gif">Existing GIF:</label>
                <?php if ($gif) : ?>
                    <img src="<?php echo $gif; ?>" alt="Land GIF" style="width: 150px; height: 150px; margin-right: 10px;">
                <?php endif; ?>
            </div>

            <!-- Option to add new images -->
            <div class="form-group">
                <label for="new_images">Add New Images:</label>
                <input type="file" class="form-control-file" id="new_images" name="new_images[]" multiple accept="image/*">
            </div>
            <!-- Option to add new GIF -->
            <div class="form-group">
                <label for="new_gif">Add New GIF:</label>
                <input type="file" class="form-control-file" id="new_gif" name="new_gif" accept="image/gif">
            </div>

            <!-- Option to remove images or GIF -->
            <div class="form-group">
                <label>Remove Existing Images/GIF:</label><br>
                <?php foreach ($images as $index => $image) : ?>
                    <input type="checkbox" name="remove_image[]" value="<?php echo $index; ?>"> Image <?php echo $index + 1; ?><br>
                <?php endforeach; ?>
                <?php if ($gif) : ?>
                    <input type="checkbox" name="remove_gif" value="1"> GIF<br>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
            <a href="sell.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
