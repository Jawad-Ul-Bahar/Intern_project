<?php
session_start();
include("connection.php");

$uid = $_SESSION['uid'] ?? 0;
$session_id = session_id();

if (isset($_GET['proid'])) {
    $proid = intval($_GET['proid']);

    // Session se remove
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['proid'] == $proid) {
                unset($_SESSION['cart'][$key]);
                break;
            }
        }
    }

    // DB se remove
    mysqli_query($con, "DELETE FROM cart WHERE proid = '$proid' AND (uid = '$uid' OR session_id = '$session_id') AND tracking_number IS NULL");
}

header("Location: your_orders.php");
exit();
