<?php
include("query.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer.php';
require 'SMTP.php';
require 'Exception.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$tracking_number = $_GET['tracking_number'] ?? '';

if (empty($tracking_number)) {
    echo "<script>alert('Invalid Tracking Number'); window.location.href='index.php';</script>";
    exit;
}

$order_q = mysqli_query($con, "SELECT * FROM orders WHERE tracking_number = '$tracking_number'");
$orders = mysqli_fetch_all($order_q, MYSQLI_ASSOC);

if (empty($orders)) {
    echo "<script>alert('Order not found'); window.location.href='index.php';</script>";
    exit;
}

$first_order = $orders[0];
$order_id = $first_order['id'];

// Cancel Logic
$cancel_success = false;
if (isset($_POST['cancel_order'])) {
    $reason = mysqli_real_escape_string($con, $_POST['cancel_reason'] ?? 'Not specified');

    $update = mysqli_query($con, "UPDATE orders SET order_status = 'cancelled', cancel_reason = '$reason' WHERE tracking_number = '$tracking_number'");
    if ($update) {
        $cancel_success = true;

        // Send Email to Admin
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'jawadulbahar@gmail.com';
            $mail->Password = 'delwgxougwyvdpzi';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('jawadulbahar@gmail.com', 'Order System');
            $mail->addAddress('jawadulbahar@gmail.com');

            $mail->isHTML(true);
            $mail->Subject = 'Order Cancelled by Customer';
            $mail->Body = "
                <h3>Order Cancelled</h3>
                <p><strong>Customer:</strong> {$first_order['uname']} ({$first_order['email']})</p>
                <p><strong>Phone:</strong> {$first_order['work_phone']}</p>
                <p><strong>Tracking No:</strong> {$first_order['tracking_number']}</p>
                <p><strong>Cancelled At:</strong> " . date("Y-m-d H:i:s") . "</p>
                <p><strong>Reason:</strong> $reason</p>
            ";

            $mail->send();
        } catch (Exception $e) {
            // silently fail
        }

        // Update all in local array too
        foreach ($orders as &$o) {
            $o['order_status'] = 'cancelled';
            $o['cancel_reason'] = $reason;
        }
    }
}

$order_time = strtotime($first_order['order_time']);
$now = time();
$time_diff = ($now - $order_time) / 60;
$show_cancel = ($time_diff <= 60) && ($first_order['order_status'] === 'pending');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Thank You</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="images/icons/zufelogo-removebg-preview.png"/>
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/main.css">
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
        .thankyou-wrapper {
            background: linear-gradient(135deg, #f9f9f9, #ffffff);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        .thankyou-img {
            max-height: 250px;
            object-fit: cover;
            border-radius: 10px;
        }
        .thankyou-alert {
            background-color: #e6f7ff;
            border-left: 6px solid #009efb;
            padding: 15px;
            margin-top: 20px;
            border-radius: 8px;
        }
        .order-card {
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>

<?php include("header.php"); ?>

<section class="bg0 p-t-80 p-b-100 mt-4">
    <div class="container">
        <div class="row justify-content-center align-items-center">
            <div class="col-lg-10">
                <div class="thankyou-wrapper">
                    <h3 class="text-center text-success mb-5">🎉 Thank You for Your Order!</h3>

                    <?php if ($cancel_success): ?>
                        <div class="alert alert-danger text-center">
                            Your order has been cancelled.
                        </div>
                    <?php endif; ?>

                    <?php if ($first_order['order_status'] === 'cancelled' && !empty($first_order['cancel_reason'])): ?>
                        <div class="alert alert-warning">
                            <strong>Cancelled Reason:</strong> <?= htmlspecialchars($first_order['cancel_reason']) ?>
                        </div>
                    <?php endif; ?>
                        <?php
                        $total_amount = 0;
                        foreach ($orders as $order) {
                            $total_amount += $order['proprice'] * $order['proqty'];
                        }

                        $shipping_charges = $first_order['shipping_charges'];
                        $grand_total = $total_amount + $shipping_charges;
                        ?>

                        <?php foreach ($orders as $order): ?>
                            <div class="order-card row align-items-center">
                                <div class="col-md-5 text-center mb-3 mb-md-0">
                                    <img src="../adminpanel3/img/<?= $order['product_img'] ?>" alt="Product Image" class="img-fluid thankyou-img">
                                </div>
                                <div class="col-md-7">
                                    <p><strong>Product:</strong> <?= $order['proname'] ?></p>
                                    <p><strong>Price:</strong> Rs. <?= $order['proprice'] ?></p>
                                    <p><strong>Quantity:</strong> <?= $order['proqty'] ?></p>
                                    <p> <?= $order['selected_color'] ?> / 
                                    <?= $order['selected_size'] ?> /  
                                    <?= $order['shirt_type'] ?></p>

                                </div>
                            </div>
                        <?php endforeach; ?>

                                            <div class="mt-4">
                                                <div class="order-summary-box" style="background: #f9f9f9; padding: 20px; border-radius: 10px; border: 1px solid #ddd;">
                            <div class="order-summary-box" style="background: #f9f9f9; padding: 20px; border-radius: 10px; border: 1px solid #ddd;">
                        <h4 class="mb-3">Order Summary</h4>

                        <p><strong>Status:</strong> <?= ucfirst($first_order['order_status']) ?></p>
                        <p><strong>Area:</strong> <?= $first_order['delivery_area'] ?></p>
                        <p><strong>Tracking Code:</strong> <?= $first_order['tracking_number'] ?></p>

                        <hr>

                        <?php
                            // Calculate subtotal from all items
                            $subtotal = 0;
                            $total_tax = 0; // ✅ New variable for total tax
                            foreach ($orders as $order) {
                                $subtotal += $order['proprice'] * $order['proqty'];
                                $total_tax += (float) $order['item_tax']; // ✅ Adding tax for each product
                            }

                            $shipping = (float) $first_order['shipping_charges'];
                            $grand_total = $subtotal + $shipping + $total_tax; // ✅ Using the new total tax
                            ?>

                            <p><strong>Subtotal (Items):</strong> Rs. <?= number_format($subtotal, 2) ?></p>
                            <p><strong>Shipping:</strong> Rs. <?= number_format($shipping, 2) ?></p>

                            <?php if ($total_tax > 0): // ✅ Condition check with new variable ?>
                                <p><strong>Tax:</strong> Rs. <?= number_format($total_tax, 2) ?></p>
                            <?php endif; ?>

                            <hr>

                            <p><strong>Total:</strong> Rs. <?= number_format($grand_total, 2) ?></p>

                            <div class="thankyou-alert">
                                Your order has been <strong>successfully placed</strong>.<br>
                                Once we <strong>approve</strong> your order, you’ll be able to check its status on the
                                <a href="track_order.php" class="text-primary font-weight-bold">tracking page</a>
                                using your tracking code.
                            </div>
                        </div>

                    <?php if ($show_cancel): ?>
                        <form method="POST" class="mt-4" id="cancelForm">
                            <button type="button" id="cancelBtn" class="btn btn-danger w-100">Cancel Order</button>

                            <div id="cancelReasonWrap" style="display: none;" class="mt-3">
                                <div class="form-group">
                                    <label for="cancel_reason">Why are you cancelling this order?</label>
                                    <textarea name="cancel_reason" id="cancel_reason" class="form-control" rows="4" placeholder="Write your reason..."></textarea>
                                </div>
                                <button type="submit" name="cancel_order" id="confirmCancelBtn" class="btn btn-danger w-100 mt-2" disabled>
                                    Confirm Cancellation
                                </button>
                                <small class="text-muted d-block mt-2">You can cancel this order within 1 hour of placing it.</small>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>    
</section>

<?php include("footer.php"); ?>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const cancelBtn = document.getElementById('cancelBtn');
        const cancelWrap = document.getElementById('cancelReasonWrap');
        const cancelReason = document.getElementById('cancel_reason');
        const confirmBtn = document.getElementById('confirmCancelBtn');

        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => {
                cancelWrap.style.display = 'block';
                cancelBtn.style.display = 'none';
                cancelReason.focus();
            });
        }

        if (cancelReason) {
            cancelReason.addEventListener('input', () => {
                confirmBtn.disabled = cancelReason.value.trim().length < 5;
            });
        }
    });
</script>
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
