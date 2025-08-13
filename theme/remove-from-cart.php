<?php
session_start();
include_once("connection.php");

if (isset($_POST['index'])) {
    $index = $_POST['index'];

    if (isset($_SESSION['cart'][$index])) {
        $proid = $_SESSION['cart'][$index]['proid'];

        // Remove from session
        unset($_SESSION['cart'][$index]);

        // Also remove from DB if exists
        $uid = $_SESSION['user_id'] ?? 0;
        $session_id = session_id();

        $delete_query = "DELETE FROM cart WHERE proid = '$proid' AND tracking_number IS NULL AND (uid = '$uid' OR session_id = '$session_id')";
        mysqli_query($con, $delete_query);

        echo "success";
        exit;
    }
}

echo "error";
?>
