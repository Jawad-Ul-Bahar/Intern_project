<?php
include("query.php");


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ If user updated dropdowns (color/size/gender)
if (isset($_POST['update_options'])) {
    foreach ($_POST['sizes'] as $proid => $size) {
        $_SESSION['cart'][$proid]['sizes'] = $size;
    }
    foreach ($_POST['colors'] as $proid => $color) {
        $_SESSION['cart'][$proid]['colors'] = $color;
    }
    // ✅ New shirttype update
    foreach ($_POST['shirttypes'] as $proid => $shirttype) {
        $_SESSION['cart'][$proid]['shirttype'] = $shirttype;
    }

    echo "<script>alert('Selections updated successfully!'); location.reload();</script>";
    exit();
}

// ✅ Area shipping logic
$selected_area = $_POST['area'] ?? ($_SESSION['selected_area'] ?? '');
$delivery_charges = 0;
if ($selected_area != '') {
    $_SESSION['selected_area'] = $selected_area;
    $delivery_q = mysqli_query($con, "SELECT charges FROM delivery_settings WHERE area_name = '$selected_area' LIMIT 1");
    $delivery_row = mysqli_fetch_assoc($delivery_q);
    $delivery_charges = $delivery_row['charges'] ?? 0;
}

// ✅ Tax and grand total logic
$alltotal = 0;
$total_tax = 0;
foreach ($_SESSION['cart'] as $key => $val) {
    $subtotal = $val['proprice'] * $val['proqty'];
    $alltotal += $subtotal;

    $proid = $val['proid'] ?? null;

    $tax_q = mysqli_query($con, "SELECT tax_percent, show_tax FROM products WHERE id = '$proid'");
    $tax_data = mysqli_fetch_assoc($tax_q);
    $tax_percent = $tax_data['tax_percent'] ?? 0;
    $show_tax = $tax_data['show_tax'] ?? 0;

    $item_tax = 0;
    if ($show_tax == 1 && $tax_percent > 0) {
        $item_tax = ($subtotal * $tax_percent) / 100;
        $total_tax += $item_tax;
    }

    $_SESSION['cart'][$key]['calculated_tax'] = $item_tax;
}
$grand_total = $alltotal + $total_tax + $delivery_charges;


// ✅ Order placed logic - Updated version
if (isset($_POST['checkout'])) {
    $uid = $_SESSION['userid'] ?? 0;
    $uname = $_SESSION['username'] ?? 'Guest';
    $name = $_POST['name'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $work_phone = $_POST['work_phone'];
    $city = $_POST['city'];
    $country = $_POST['country'];
    $postal_code = $_POST['postal_code'];
    $home_phone = $_POST['home_phone'];

    $session_id = session_id();

    function generateTrackingNumber($length = 10) {
        return strtoupper(substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length));
    }

    $tracking_number = generateTrackingNumber();
    $allInserted = true;

    // ✅ Fetch cart items from cart table instead of session
    $cart_query = mysqli_query($con, "SELECT * FROM cart WHERE (uid = '$uid' OR session_id = '$session_id') AND tracking_number IS NULL");

    while ($val = mysqli_fetch_assoc($cart_query)) {
        $proid = $val['proid'];
        $proname = $val['proname'];
        $proprice = $val['proprice'];
        $proqty = $val['proqty'];
        $color = $val['selected_color'] ?? '';
        $size = $val['selected_size'] ?? '';
        $shirttype = $val['shirt_type'] ?? '';
        $proimg = $val['proimg'] ?? 'default.jpg';

        $area = $_SESSION['selected_area'] ?? '';
        $shipping = $delivery_charges ?? 0;

        // ✅ Calculate tax if needed
        $subtotal = $proprice * $proqty;
        $tax_q = mysqli_query($con, "SELECT tax_percent, show_tax FROM products WHERE id = '$proid'");
        $tax_data = mysqli_fetch_assoc($tax_q);
        $tax_percent = $tax_data['tax_percent'] ?? 0;
        $show_tax = $tax_data['show_tax'] ?? 0;

        $item_tax = 0;
        if ($show_tax == 1 && $tax_percent > 0) {
            $item_tax = ($subtotal * $tax_percent) / 100;
        }

        $total_amount = $subtotal + $item_tax + $shipping;

        // ✅ Insert into orders table with new fields
        $insert = mysqli_query($con, "INSERT INTO `orders`
        (uid, uname, product_id, proname, proprice, proqty, selected_color, selected_size, shirt_type, name, address, email, work_phone, delivery_area, shipping_charges, item_tax, total_amount, product_img, tracking_number, city, country, postal_code, home_phone)
        VALUES 
        ('$uid', '$uname', '$proid', '$proname', '$proprice', '$proqty', '$color', '$size', '$shirttype', '$name', '$address', '$email', '$work_phone', '$area', '$shipping', '$item_tax', '$total_amount', '$proimg', '$tracking_number', '$city', '$country', '$postal_code', '$home_phone')");

        if (!$insert) {
            echo mysqli_error($con);
            $allInserted = false;
            break;
        }
    }

    if ($allInserted) {
        // ✅ Mark those cart items as "used"
        mysqli_query($con, "
            UPDATE cart 
            SET tracking_number = '$tracking_number' 
            WHERE (uid = '$uid' OR session_id = '$session_id') 
              AND tracking_number IS NULL
        ");

        // ✅ NEW: Store important info in session for order tracking
        $_SESSION['last_order_tracking'] = $tracking_number;
        $_SESSION['checkout_email'] = $email; // For guest users
        
        unset($_SESSION['cart']); // Optional: clear session cart

        echo "<script>
            alert('Order placed successfully!');
            location.assign('thankyou.php?tracking_number=$tracking_number');
        </script>";
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Shopping Cart</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="images/icons/zufelogo-removebg-preview.png"/>
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="fonts/iconic/css/material-design-iconic-font.min.css">
    <link rel="stylesheet" type="text/css" href="fonts/linearicons-v1.0.0/icon-font.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
    <link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/animsition/css/animsition.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" type="text/css" href="css/util.css">
    <link rel="stylesheet" type="text/css" href="css/main.css">
</head>

<body class="animsition">
    <div class="container"><div class="bread-crumb flex-w p-l-25 p-r-15 p-t-30 p-lr-0-lg"></div></div><br>

    <div class="container mt-4">
        <div class="row">
            <?php include("header.php"); ?>
            <!-- LEFT SIDE -->
            <div class="col-sm-12 col-lg-7 col-xl-7 m-lr-auto m-b-50">
                <div class="bor10 p-lr-40 p-t-30 p-b-40 m-r-63 m-l-40 m-lr-0-xl p-lr-15-sm">
                    <div class="flex-w flex-t bor12 p-t-15 p-b-30">
                        <div class="size-208 w-full-ssm">
                            <form class="bg0 p-t-15" method="POST" action="">
                                <span class="stext-112 cl8 text-left">Personal Information</span>

                                <div style="width: 470px;" class="bor8 bg0 m-b-12">
                                    <input class="stext-111 cl8 plh3 size-111 p-lr-15" type="text" name="name" placeholder="Name" required>
                                </div><br>

                                <div class="bor8 bg0 m-b-12">
                                    <select name="area" class="stext-111 cl8 plh3 size-111 p-lr-15" onchange="this.form.submit();" required>
                                        <option value="">Select Delivery Area</option>
                                        <?php 
                                        $areas = mysqli_query($con, "SELECT DISTINCT area_name FROM delivery_settings");
                                        while ($row = mysqli_fetch_assoc($areas)) {
                                            $selected = ($selected_area == $row['area_name']) ? 'selected' : '';
                                            echo "<option value='{$row['area_name']}' $selected>{$row['area_name']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div style="width: 470px;" class="bor8 bg0 m-b-12">
                                    <input class="stext-111 cl8 plh3 size-111 p-lr-15" type="text" name="address" placeholder="Address" required>
                                </div><br>

                                <div style="width: 470px;" class="bor8 bg0 m-b-12">
                                    <input class="stext-111 cl8 plh3 size-111 p-lr-15" type="text" name="city" placeholder="City" required>
                                </div><br>

                                <div style="width: 470px;" class="bor8 bg0 m-b-12">
                                    <input class="stext-111 cl8 plh3 size-111 p-lr-15" type="text" name="country" placeholder="Country" required>
                                </div><br>

                                <div style="width: 470px;" class="bor8 bg0 m-b-12">
                                    <input class="stext-111 cl8 plh3 size-111 p-lr-15" type="text" name="postal_code" placeholder="Postal Code" required>
                                </div><br>

                                <div style="width: 470px;" class="bor8 bg0 m-b-12">
                                    <input class="stext-111 cl8 plh3 size-111 p-lr-15" type="email" name="email" placeholder="E-mail" required>
                                </div><br>

                                <div style="width: 470px;" class="bor8 bg0 m-b-12">
                                    <input class="stext-111 cl8 plh3 size-111 p-lr-15" type="tel" name="work_phone" placeholder="Work Phone" required>
                                </div><br>

                                <div style="width: 470px;" class="bor8 bg0 m-b-12">
                                    <input class="stext-111 cl8 plh3 size-111 p-lr-15" type="tel" name="home_phone" placeholder="Home Phone" required>
                                </div><br>

                                <div class="mt-4" style="width: 470px;">
                                    <button type="submit" name="checkout"
                                        class="w-full stext-101 cl2 bg8 bor13 hov-btn3 p-lr-15 p-tb-10 trans-04 pointer">
                                        Submit Order
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT SIDE - Order Summary -->
            <div class="col-sm-12 col-lg-5 col-xl-5 m-lr-auto m-b-50">
                <div class="bor10 p-4 mb-4 shadow-sm">
                    <h4 class="mtext-109 cl2 p-b-30">Cart Totals</h4>

                    <?php foreach ($_SESSION['cart'] as $key => $val): ?>
    <div class="d-flex align-items-start mb-3 pb-3 border-bottom position-relative">
        <!-- Product Image with Quantity Badge -->
        <div style="position: relative;">
            <img src="../adminpanel3/img/<?php echo $val['proimg']; ?>" class="rounded me-3" style="width: 90px; height: 90px; object-fit: cover;" alt="Product Image">
            
            <!-- Qty Badge -->
            <span style="position: absolute; top: -15px; right: -10px; background: #726969ff; color: white; padding: 4px 6px; border-radius: 50%; font-size: 10px; font-weight: bold;">
                x<?php echo $val['proqty']; ?>
            </span>
            
        </div>&nbsp;&nbsp;

        <!-- Product Info -->
        <div class="flex-grow-1 mt-2 d-flex justify-content-between align-items-start w-100">
    <div>
        <!-- Product Name -->
        <p class="mb-1 font-weight-bold"><?php echo $val['proname']; ?></p>

        <!-- Formatted Options Line -->
        <?php
            $details = [];

            if (!empty($val['selected_color'])) {
                $details[] =  ucfirst($val['selected_color']);
            }

            if (!empty($val['selected_size'])) {
                $details[] =  ucfirst($val['selected_size']);
            }

            if (!empty($val['shirttype'])) {
                $details[] =  ucfirst($val['shirttype']);
            }

            if (!empty($details)) {
                echo implode(' / ', $details) . '</small>';
            }
        ?>
        <hr>
    </div>

    <!-- Price -->
    <strong class="text-right">Rs. <?php echo $val['proprice'] * $val['proqty']; ?></strong>
</div>

    </div>
    <hr>
<?php endforeach; ?>
<?php
$subtotal_items = 0;

foreach ($_SESSION['cart'] as $item) {
    $subtotal_items += $item['proprice'] * $item['proqty'];
}
?>



                    <div class="d-flex justify-content-between mt-2">
                        <span>Subtotal - Items</span>
<strong>Rs. <?php echo number_format($subtotal_items); ?></strong>

                    </div>
                    <div class="d-flex justify-content-between mt-3">
                        <span>Shipping</span>
                        <strong>Rs. <?php echo $delivery_charges; ?></strong>
                    </div>

                    <?php if ($total_tax > 0): ?>
                    <div class="d-flex justify-content-between mt-2">
                        <span>Tax</span>
                        <strong>Rs. <?php echo number_format($total_tax, 2); ?></strong>
                    </div>
                    <?php endif; ?>
                        <hr style= "border: none; border-top: 3px dotted #aaaaaaff;">
                    <div class="d-flex justify-content-between mt-2">
                        <span>Total</span>
                        <strong>Rs. <?php echo $grand_total; ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include('footer.php'); ?>

<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
<script src="vendor/animsition/js/animsition.min.js"></script>
<script src="vendor/bootstrap/js/popper.js"></script>
<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="vendor/select2/select2.min.js"></script>
<script>
    $(".js-select2").each(function() {
        $(this).select2({
            minimumResultsForSearch: 20,
            dropdownParent: $(this).next('.dropDownSelect2')
        });
    });
</script>
<script src="vendor/MagnificPopup/jquery.magnific-popup.min.js"></script>
<script src="vendor/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script>
    $('.js-pscroll').each(function() {
        $(this).css('position', 'relative');
        $(this).css('overflow', 'hidden');
        var ps = new PerfectScrollbar(this, {
            wheelSpeed: 1,
            scrollingThreshold: 1000,
            wheelPropagation: false,
        });
        $(window).on('resize', function() {
            ps.update();
        });
    });
</script>
<script src="js/main.js"></script>

</body>
</html>