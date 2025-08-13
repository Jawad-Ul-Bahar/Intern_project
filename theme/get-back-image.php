<?php
include('connection.php');

$product_id = $_GET['product_id'] ?? 0;
$color = $_GET['color'] ?? '';

$product_id = intval($product_id);
$color = strtolower(trim($color));

$q = mysqli_query($con, "SELECT image FROM product_images WHERE product_id = $product_id AND LOWER(color) = '$color' AND type = 'back'");

if ($row = mysqli_fetch_assoc($q)) {
    echo "../adminpanel3/img/" . $row['image'];
} else {
    echo ""; // fallback
}
?>
