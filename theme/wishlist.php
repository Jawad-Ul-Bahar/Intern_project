<?php
session_start();
include('connection.php');

// Fallback user_id = session id (for non-logged in users)
if (!isset($_SESSION['guest_id'])) {
    $_SESSION['guest_id'] = rand(100000, 999999); // temp guest user ID
}
$user_id = $_SESSION['guest_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);

    // ✅ Add quotes around $user_id to avoid SQL issues
    $check = mysqli_query($con, "SELECT * FROM wishlist WHERE user_id='$user_id' AND product_id=$product_id");

    if (mysqli_num_rows($check) > 0) {
        echo "exists";
    } else {
        $insert = mysqli_query($con, "INSERT INTO wishlist(user_id, product_id) VALUES('$user_id', $product_id)");
        
        if ($insert) {
            echo "added";
        } else {
            echo "error";
            error_log("Wishlist Insert Error: " . mysqli_error($con));
        }
    }
}
?>
