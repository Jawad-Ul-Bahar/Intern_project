<?php
include("connection.php");

if (isset($_POST['rating']) && isset($_POST['product_id'])) {
    $rating = intval($_POST['rating']);
    $product_id = intval($_POST['product_id']);

    // Insert rating
    $query = "INSERT INTO ratings (product_id, rating) VALUES ($product_id, $rating)";
    if (mysqli_query($con, $query)) {
        echo "Rating saved successfully";
    } else {
        echo "Error saving rating";
    }
} else {
    echo "Invalid data";
}
?>
