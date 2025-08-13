<?php
session_start();

// Handle only POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get product details from POST
    $id    = $_POST['id'];
    $name  = $_POST['name'];
    $price = $_POST['price'];

    // Optional fields from form (ensure they are passed correctly)
    $image       = $_POST['image'] ?? '';
    $sizes       = $_POST['selected_size'] ?? '';
    $colors      = $_POST['selected_color'] ?? '';
    $shirt_type  = $_POST['shirt_type'] ?? '';
    $description = $_POST['description'] ?? '';

    // Create product array
    $product = [
    'proid'          => $id,
    'proname'        => $name,
    'proprice'       => $price,
    'proqty'         => 1,
    'proimg'         => $image,
    'selected_size'  => $sizes,
    'selected_color' => $colors,
    'shirttype'      => $shirt_type,
    'description'    => $description
];


    // Initialize cart if it doesn't exist
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Check if product already exists in cart
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['proid'] == $id) {
            $item['proqty'] += 1; // increment quantity
            $found = true;
            break;
        }
    }

    // If product not found, add to cart
    if (!$found) {
        $_SESSION['cart'][] = $product;
    }

    // Return cart count (used for cart badge)
    echo count($_SESSION['cart']);
}
?>
