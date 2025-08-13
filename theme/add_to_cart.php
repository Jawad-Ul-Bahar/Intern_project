<?php
session_start();
include("connection.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['proid'])) {
    $proid = intval($_POST['proid']);
    $proname = $_POST['proname'];
    $proprice = floatval($_POST['proprice']);
    $proqty = intval($_POST['proqty'] ?? 1);
    $proimg = $_POST['proimg'];
    $size = $_POST['selected_size'] ?? '';
    $color = $_POST['selected_color'] ?? '';
    $shirttype = $_POST['shirttype'] ?? ''; // ✅ add this

    // Identify user
    $uid = $_SESSION['uid'] ?? 0;
    $session_id = session_id();

    // Store in SESSION cart
    $_SESSION['cart'][] = [
        'proid' => $proid,
        'proname' => $proname,
        'proprice' => $proprice,
        'proqty' => $proqty,
        'proimg' => $proimg,
        'selected_size' => $size,
        'selected_color' => $color,
        'shirttype' => $shirttype // ✅ add this
    ];

    // Check if already in DB cart
    $check = mysqli_query($con, "SELECT * FROM cart WHERE proid='$proid' AND tracking_number IS NULL AND (uid='$uid' OR session_id='$session_id')");

    if (mysqli_num_rows($check) == 0) {
        mysqli_query($con, "INSERT INTO cart 
            (uid, session_id, proid, proname, proprice, proqty, proimg, selected_size, selected_color, shirt_type, created_at) 
            VALUES 
            ('$uid', '$session_id', '$proid', '$proname', '$proprice', '$proqty', '$proimg', '$size', '$color', '$shirttype', NOW())");
    }

    header("Location: index.php");
    exit();
} else {
    echo "Invalid Request";
}
