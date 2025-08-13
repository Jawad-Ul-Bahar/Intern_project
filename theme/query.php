<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("connection.php");

// -------------------
// USER LOGIN
// -------------------
if (isset($_POST['btnn_login'])) {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $query = mysqli_query($con, "SELECT * FROM `users` WHERE email='$email' AND pass='$pass' AND role='user'");
    $row = mysqli_fetch_assoc($query);

    if ($row) {
        $_SESSION['userid']   = $row['id'];
        $_SESSION['username'] = $row['fname'];
        $_SESSION['userLname'] = $row['lname'];
        echo "<script>location.assign('index.php')</script>";
        exit;
    } else {
        echo "<h3>Login failed...!</h3>";
        exit;
    }
}

// -------------------
// ADD TO CART
// -------------------
if (isset($_POST['proid'])) {
    $proid       = $_POST['proid'];
    $proname     = $_POST['proname'];
    $proprice    = $_POST['proprice'];
    $proqty      = $_POST['proqty'];
    $proimg      = $_POST['proimg'];
    $colors      = $_POST['colors'];
    $sizes       = $_POST['sizes'];
    $shirttype   = $_POST['shirttype'];
    $description = $_POST['description'];

    // Initialize cart if not set
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $found = false;

    foreach ($_SESSION['cart'] as &$item) {
        if (
            $item['proid'] == $proid &&
            $item['colors'] == $colors &&
            $item['sizes'] == $sizes &&
            $item['shirttype'] == $shirttype
        ) {
            // Same product found → increase quantity
            $item['proqty'] += $proqty;
            $found = true;
            break;
        }
    }
    unset($item); // Break reference

    if (!$found) {
        // Add new product
        $_SESSION['cart'][] = [
            'proid'       => $proid,
            'proname'     => $proname,
            'proprice'    => $proprice,
            'proqty'      => $proqty,
            'proimg'      => $proimg,
            'colors'      => $colors,
            'sizes'       => $sizes,
            'shirttype'   => $shirttype,
            'description' => $description
        ];
    }

    echo "<script>alert('Product added to cart successfully!'); location.assign('index.php');</script>";
    exit;
}
?>
