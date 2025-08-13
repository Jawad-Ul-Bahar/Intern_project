<?php 
include("connection.php"); 
session_start();

$uid = $_SESSION['uid'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Your Orders</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSS Links -->
    <link rel="icon" type="image/png" href="images/icons/zufelogo-removebg-preview.png"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="fonts/iconic/css/material-design-iconic-font.min.css">
    <link rel="stylesheet" type="text/css" href="fonts/linearicons-v1.0.0/icon-font.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
    <link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/animsition/css/animsition.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/daterangepicker/daterangepicker.css">
    <link rel="stylesheet" type="text/css" href="vendor/slick/slick.css">
    <link rel="stylesheet" type="text/css" href="vendor/MagnificPopup/magnific-popup.css">
    <link rel="stylesheet" type="text/css" href="vendor/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" type="text/css" href="css/util.css">
    <link rel="stylesheet" type="text/css" href="css/main.css">

    <style>
        .order-section {
            padding: 40px 0 60px;
            background-color: #f8f9fa;
        }

        .order-card {
            background: #fff;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 10px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.05);
        }

        .order-img {
            width: 100%;
            max-width: 100px;
            border-radius: 8px;
        }

        .order-title {
            font-weight: 600;
            font-size: 18px;
        }

        .order-detail {
            font-size: 14px;
            color: #666;
            line-height: 1.6;
        }

        .order-meta {
            text-align: center;
            font-size: 14px;
            color: #666;
        }

        .order-meta div {
            margin-bottom: 5px;
        }

        .order-price {
            font-weight: 700;
            font-size: 16px;
            color: #000;
            text-align: right;
        }

        .bottom-actions {
            text-align: right;
            margin-top: 30px;
        }

        .bottom-actions .subtotal {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .bottom-actions .btn {
            background-color: #321961;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 8px 20px;
            font-size: 14px;
            margin-left: 10px;
        }

        .bottom-actions .btn:hover {
            background-color: #22124e;
        }
    </style>
</head>
<body class="animsition">

<?php include("header.php"); ?>

<!-- Orders Section -->
<section class="order-section">
    <div class="container">
        <h2 class="ltext-105 text-dark txt-center mb-4">Shopping Basket</h2>

        <?php
        $subtotal = 0;
        $subtotal = 0;

// Collect product IDs from DB to avoid duplicates
$db_cart_ids = [];
$checkQuery = mysqli_query($con, "SELECT proid FROM cart WHERE (uid='$uid' OR session_id='" . session_id() . "') AND tracking_number IS NULL");
while ($row = mysqli_fetch_assoc($checkQuery)) {
    $db_cart_ids[] = $row['proid'];
}

        // Show Cart Items First (from session)
       // Show Cart Items First (from session)
if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
    foreach ($_SESSION['cart'] as $key => $item) {
        if (in_array($item['proid'], $db_cart_ids)) {
            continue; // skip session item if it's already in DB
        }
        $totalPrice = $item['proprice'] * $item['proqty'];
        $subtotal += $totalPrice;

        ?>
        <div class="order-card">
            <div class="row align-items-center">
                <div class="col-md-2 col-4">
                    <img src="../adminpanel3/img/<?php echo $item['proimg']; ?>" class="order-img" alt="Product Image">
                </div>
                <div class="col-md-4 col-8">
                    <div class="order-title"><?php echo $item['proname']; ?></div>
                    <div class="order-detail">Size: <?php echo $item['selected_size'] ?? 'N/A'; ?></div>
                    <div class="order-detail">Color: <?php echo $item['selected_color'] ?? 'N/A'; ?></div>
                    <div class="order-detail">Qty: <?php echo $item['proqty']; ?></div>
                </div>
                <div class="col-md-3 text-center mt-3 mt-md-0">
                    <div class="order-meta">
                        <div>Status: <span class="text-warning">In Cart</span></div>
                        <div>—</div>
                    </div>
                </div>
                <div class="col-md-3 text-end mt-3 mt-md-0">
                    <div class="order-price d-flex justify-content-end align-items-center" style="gap: 12px;">
                        <span>PKR <?php echo number_format($totalPrice); ?></span>
                        <a href="delete_order.php?index=<?php echo $key; ?>" onclick="return confirm('Remove item from cart?')">
                            <i class="fas fa-trash-alt text-danger" style="font-size: 18px;"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php
            }
        }

        // Show Placed Orders
// Show Cart Items from DB
$query = mysqli_query($con, "SELECT * FROM cart WHERE (uid='$uid' OR session_id='" . session_id() . "') AND tracking_number IS NULL ORDER BY id DESC");
if (mysqli_num_rows($query) > 0) {
    while ($row = mysqli_fetch_assoc($query)) {
        $totalOrder = $row['proprice'] * $row['proqty'];
        $subtotal += $totalOrder;
?>
        <div class="order-card">
            <div class="row align-items-center">
                <div class="col-md-2 col-4">
                    <img src="../adminpanel3/img/<?php echo $row['proimg']; ?>" class="order-img" alt="Product Image">
                </div>
                <div class="col-md-4 col-8">
                    <div class="order-title"><?php echo $row['proname']; ?></div>
                    <div class="order-detail">Size: <?php echo $row['selected_size'] ?? 'N/A'; ?></div>
                    <div class="order-detail">Color: <?php echo $row['selected_color'] ?? 'N/A'; ?></div>
                    <div class="order-detail">Qty: <?php echo $row['proqty']; ?></div>
                </div>
                <div class="col-md-3 text-center mt-3 mt-md-0">
                    <div class="order-meta">
                        <div>Status: <span class="text-warning">In Cart</span></div>
                        <div>Date: <?php echo date("Y-m-d", strtotime($row['created_at'])); ?></div>
                    </div>
                </div>
                <div class="col-md-3 text-end mt-3 mt-md-0">
                    <div class="order-price d-flex justify-content-end align-items-center" style="gap: 12px;">
                        <span>PKR <?php echo number_format($totalOrder); ?></span>
                            <a href="delete_order.php?proid=<?php echo $item['proid']; ?>" onclick="return confirm('Remove item from cart?')">
                            <a href="delete_order.php?proid=<?php echo $row['proid']; ?>" onclick="return confirm('Remove item from cart?')">
                            <i class="fas fa-trash-alt text-danger" style="font-size: 18px;"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
<?php
    }
}


        if ($subtotal == 0) {
            echo '<div class="text-center">No items found in cart or orders.</div>';
        }
        ?>

        <?php if ($subtotal > 0): ?>
        <hr>
        <div class="bottom-actions">
            <div class="subtotal">Subtotal: <span>PKR <?php echo number_format($subtotal); ?></span>
            <small class="text-muted"><p>Taxes & shipping calculated at checkout</p></small>
        </div>
            <div><a href="shoping-cart.php" class="btn">Checkout</a></div>
            <div><a href="index.php" class="btn mt-2">Continue Shopping</a></div>
        </div>
        <?php endif; ?>

    </div>
</section>

<?php include("footer.php"); ?>

<!-- Scripts -->
<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
<script src="vendor/animsition/js/animsition.min.js"></script>
<script src="vendor/bootstrap/js/popper.js"></script>
<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="vendor/select2/select2.min.js"></script>
<script>$(".js-select2").select2({minimumResultsForSearch: 20, dropdownParent: $('.dropDownSelect2')});</script>
<script src="vendor/daterangepicker/moment.min.js"></script>
<script src="vendor/daterangepicker/daterangepicker.js"></script>
<script src="vendor/slick/slick.min.js"></script>
<script src="js/slick-custom.js"></script>
<script src="vendor/MagnificPopup/jquery.magnific-popup.min.js"></script>
<script src="vendor/isotope/isotope.pkgd.min.js"></script>
<script src="vendor/sweetalert/sweetalert.min.js"></script>
<script src="vendor/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>
