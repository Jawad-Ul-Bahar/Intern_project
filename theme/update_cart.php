<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $key = $_POST['item_key'];

    if (isset($_SESSION['cart'][$key])) {
        $_SESSION['cart'][$key]['sizes'] = $_POST['sizes'];
        $_SESSION['cart'][$key]['colors'] = $_POST['colors'];
        $_SESSION['cart'][$key]['gender'] = $_POST['gender'];
        $_SESSION['cart'][$key]['proqty'] = max(1, intval($_POST['proqty']));
    }
}

header("Location: cart.php"); // Change to your cart page name
exit();
