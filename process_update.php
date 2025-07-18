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

function updateLandDetails($conn, $landID)
{
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_SESSION['username'];
        $title = isset($_POST['title']) ? $_POST['title'] : null;
        $description = isset($_POST['description']) ? $_POST['description'] : null;
        $address = isset($_POST['address']) ? $_POST['address'] : null;
        $plotArea = isset($_POST['plotArea']) ? $_POST['plotArea'] : null;
        $dimension = isset($_POST['dimension']) ? $_POST['dimension'] : null;
        $startingBid = isset($_POST['startingBid']) ? $_POST['startingBid'] : null;

        // Handle adding new images
        $newImages = [];
        if (!empty($_FILES['new_images']['name'][0])) {
            foreach ($_FILES['new_images']['tmp_name'] as $key => $tmp_name) {
                $targetDir = "uploads/$username/";
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true); // Create the directory if it doesn't exist
                }
                $target = $targetDir . basename($_FILES['new_images']['name'][$key]);
                move_uploaded_file($tmp_name, $target);
                $newImages[] = $target;
            }
        }

        // Handle removing images
        $imagesToRemove = isset($_POST['remove_image']) ? $_POST['remove_image'] : [];
        $existingImages = explode(',', $_POST['existing_images']);
        foreach ($imagesToRemove as $index) {
            if (isset($existingImages[$index])) {
                unlink($existingImages[$index]);
                unset($existingImages[$index]);
            }
        }

        $images = array_merge($existingImages, $newImages);

        $gif = null;
        if (!empty($_FILES['new_gif']['name'])) {
            $targetDir = "uploads/$username/";
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true); 
            }
            $target = $targetDir . basename($_FILES['new_gif']['name']);
            move_uploaded_file($_FILES['new_gif']['tmp_name'], $target);
            $gif = $target;
        } elseif (isset($_POST['remove_gif']) && $_POST['remove_gif'] == "1") {
            unlink($_POST['existing_gif']);
        } else {
            $gif = $_POST['existing_gif'];
        }

        $stmt = $conn->prepare("UPDATE Land SET Title = ?, Description = ?, Address = ?, PlotArea = ?, Dimension = ?, Images = ?, GIF = ?, StartingBid = ? WHERE LandID = ?");
        if ($stmt) {
            $stmt->bind_param("ssssssssd", $title, $description, $address, $plotArea, $dimension, implode(',', $images), $gif, $startingBid, $landID);
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}

$message = "";
$landID = isset($_POST['land_id']) ? $_POST['land_id'] : null;

if ($landID) {
    if (updateLandDetails($conn, $landID)) {
        $message = "Land details updated successfully!";
        header("Location: sell.php?message=" . urlencode($message));
        exit;
    } else {
        $message = "Error updating land details.";
    }
} else {
    $message = "Land ID not provided.";
}

$conn->close();
?>
