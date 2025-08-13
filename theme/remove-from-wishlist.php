<?php
session_start();
include('connection.php');

$user_id = $_SESSION['user_id'] ?? $_SESSION['guest_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id']) && $user_id) {
    $product_id = intval($_POST['product_id']);

    // Remove from wishlist
    mysqli_query($con, "DELETE FROM wishlist WHERE user_id = '$user_id' AND product_id = '$product_id'");

    // Redirect back to wishlist
    header("Location: wishlist-view.php");
    exit();
} else {
    echo "Invalid request.";
}
