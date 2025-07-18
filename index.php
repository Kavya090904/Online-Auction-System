<?php
session_start();

require_once 'helper/session_handler.php';
require_once 'helper/db_connection.php';
require_once 'helper/land_functions.php';

$max_lands = 9;
$lands = getLatestLands($conn, $max_lands);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include('partials/menu.php'); ?>

    <div class="container mt-5">
        <h2>Latest Lands</h2>
        <div class="row">
            <?php foreach ($lands as $land): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="<?php echo $land['Images']; ?>" class="card-img-top" alt="Land Image">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $land['Title']; ?></h5>
                            <p class="card-text">Starting Bid: $<?php echo $land['StartingBid']; ?></p>
                            <a href="land_details.php?land_id=<?php echo $land['LandID']; ?>" class="btn btn-primary">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
