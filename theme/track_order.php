<?php
include("connection.php");
$trackingResult = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['tracking_number'])) {
    $track_no = mysqli_real_escape_string($con, $_POST['tracking_number']);

    // 1. Check in completed_orders
    $query = "SELECT * FROM completed_orders WHERE tracking_number = '$track_no' LIMIT 1";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) > 0) {
        // Fetch all products in the same order
        $allOrdersQuery = "SELECT * FROM completed_orders WHERE tracking_number = '$track_no'";
        $allOrdersResult = mysqli_query($con, $allOrdersQuery);

        $firstOrder = mysqli_fetch_assoc($allOrdersResult);
        mysqli_data_seek($allOrdersResult, 0); // reset pointer

       $trackingResult = "
    <div class='card shadow-sm mt-5 border-left-success'>
        <div class='card-body'>
            <h4 class='card-title text-success mb-4'>✅ Order Found</h4>
            <div class='row'>
                <div class='col-md-6 mb-3'>
                    <strong>Customer:</strong> <br>{$firstOrder['uname']}
                </div>
                <div class='col-md-6 mb-3'>
                    <strong>Status:</strong> <br>{$firstOrder['order_status']}
                </div>
                <div class='col-md-6 mb-3'>
                    <strong>Tracking Number:</strong> <br>{$firstOrder['tracking_number']}
                </div>
                <div class='col-md-6 mb-3'>
                    <strong>Phone:</strong> <br>{$firstOrder['work_phone']}
                </div>
            </div>
            <hr>
            <h5 class='text-muted mb-3'>Ordered Items:</h5>";
            
// Start collecting subtotal
$subtotal = 0;

// LOOP
while ($order = mysqli_fetch_assoc($allOrdersResult)) {
    $subtotal += $order['total_amount']; // ✅ Add to subtotal

    $trackingResult .= "
        <div class='border-bottom pb-3 mb-3'>
            <div class='row'>
                <div class='col-md-6 mb-2'>
                    <strong>Product Name:</strong> <br>{$order['proname']}
                </div>
                <div class='col-md-6 mb-2'>
                    <strong>Product ID:</strong> <br>{$order['product_id']}
                </div>
                <div class='col-md-6 mb-2'>
                    <strong>Quantity:</strong> <br>{$order['proqty']}
                </div>
                <div class='col-md-6 mb-2'>
                    <strong>Total Price:</strong> <br>{$order['total_amount']} PKR
                </div>
            </div>
        </div>";
}

// ✅ Show subtotal
$status = strtolower($firstOrder['order_status']);
$address = "{$firstOrder['address']}, {$firstOrder['delivery_area']}";

$trackingResult .= "<div class='row'>
    <div class='col-md-6 mb-3'>
        <strong>Subtotal:</strong> <br>" . number_format($subtotal) . " PKR
    </div>";

$message = "";
switch ($status) {
    case 'processing':
        $message = "Your order is being processed. It will be dispatched soon to your location: <br><strong>$address</strong>";
        break;
    case 'shipped':
        $message = "Your order has been shipped. It’s on the way to: <br><strong>$address</strong>";
        break;
    case 'out of delivery':
        $message = "Your order is out for delivery and will arrive shortly at: <br><strong>$address</strong>";
        break;
    case 'delivered':
        $message = "<strong>Your order has been delivered to:</strong> <br>$address";
        break;
    default:
        $message = "Your order status is: " . ucfirst($status) . ". <strong>Delivered To:</strong> <br>$address";
        break;
}

$trackingResult .= "
    <div class='col-md-6 mb-3'>
        <strong>Delivery Status:</strong> <br>$message
    </div>
</div>";


$trackingResult .= "</div></div>"; // Close card-body and card


    } else {
        // 2. Check if cancelled by user
        $cancelledQuery = "SELECT * FROM orders WHERE tracking_number = '$track_no' AND order_status = 'cancelled' LIMIT 1";
        $cancelledResult = mysqli_query($con, $cancelledQuery);

        if (mysqli_num_rows($cancelledResult) > 0) {
            // Cancelled by user
            $trackingResult = "
            <div class='alert alert-warning mt-4'>
                <strong>Note:</strong> You have cancelled your order yourself. Cancelled orders cannot be tracked.<br>
                For support, contact us:<br>
                <strong>Email:</strong> jawadalbahar@gmail.com<br>
                <strong>Phone:</strong> +92-3021357103
            </div>";
        } else {
            // 3. Possibly deleted by admin
            $existQuery = "SELECT * FROM all_orders_backup WHERE tracking_number = '$track_no' LIMIT 1";
            $existResult = mysqli_query($con, $existQuery);

            if (mysqli_num_rows($existResult) > 0) {
                $order = mysqli_fetch_assoc($existResult);
                $trackingResult = "
                <div class='alert alert-danger mt-4'>
                    <h4>Order Cancelled</h4>
                    <p>Hello <strong>{$order['uname']}</strong>,</p>
                    <p>We regret to inform you that your order <strong>{$track_no}</strong> has been cancelled by our team due to unforeseen circumstances.</p>
                    <p>If you have any questions, feel free to reach out:</p>
                    <p><strong>Email:</strong> jawadalbahar@gmail.com<br>
                    <strong>Phone:</strong> +92-3021357103</p>
                    <p>Thank you,<br>Support Team</p>
                </div>";
            } else {
                // 4. Not found at all
                $trackingResult = "<div class='alert alert-danger mt-4'>❌ Tracking number not found. Please check and try again.</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Track Order</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" type="image/png" href="images/icons/zufelogo-removebg-preview.png"/>
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="fonts/iconic/css/material-design-iconic-font.min.css">
    <link rel="stylesheet" href="fonts/linearicons-v1.0.0/icon-font.min.css">
    <link rel="stylesheet" href="vendor/animate/animate.css">
    <link rel="stylesheet" href="vendor/css-hamburgers/hamburgers.min.css">
    <link rel="stylesheet" href="vendor/animsition/css/animsition.min.css">
    <link rel="stylesheet" href="vendor/select2/select2.min.css">
    <link rel="stylesheet" href="vendor/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="css/util.css">
    <link rel="stylesheet" href="css/main.css">

    <style>
        body {
            background-color: #f8f9fa;
        }
        .tracking-form {
            background: #fff;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
        }
        .tracking-form input {
            border-radius: 10px;
            border: 1px solid #ccc;
        }
        .tracking-form button {
            background-color: #321961;
            border: none;
            border-radius: 10px;
        }
        .card {
            border-radius: 12px;
        }
        .border-left-success {
            border-left: 5px solid #28a745 !important;
        }
        .text-muted {
            font-size: 18px !important;
        }
    </style>
</head>

<body class="animsition">

<?php include("header.php"); ?>

<section class="bg0 p-t-80 p-b-80 mt-4">
    <div class="container mt-4">
        <div class="row">
            <!-- Left: Tracking Form & Result -->
            <div class="col-lg-6 col-md-12 mb-4 mt-4">
                <div class="tracking-form">
                    <h4 class="ltext-105 text-dark txt-center mb-4">Track Your Order</h4>
                    <p class="text-center text-muted mb-5">Enter your tracking number below to check your order status.</p>

                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="tracking_number"><strong>Tracking Number</strong></label>
                            <input type="text" class="form-control form-control-lg" name="tracking_number" id="tracking_number" placeholder="e.g., TRK123456" required>
                        </div>
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-lg text-white px-5" style="background-color: #321961;">Track Order</button>
                        </div>
                    </form>

                    <?= $trackingResult ?>
                </div>
            </div>

            <!-- Right: Image Card -->
            <div class="col-lg-6 col-md-12 mb-4">
                <div class="card shadow-sm h-100 border-0" style="border-radius: 15px;">
                    <div class="card-body p-0">
                        <img src="images/banordtra.png" alt="FAQs Visual" class="img-fluid w-100" style="height: 100%; object-fit: cover; border-radius: 15px;">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include("footer.php"); ?>

<!-- Scripts -->
<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
<script src="vendor/animsition/js/animsition.min.js"></script>
<script src="vendor/bootstrap/js/popper.js"></script>
<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="vendor/select2/select2.min.js"></script>
<script>
    $(".js-select2").each(function(){
        $(this).select2({
            minimumResultsForSearch: 20,
            dropdownParent: $(this).next('.dropDownSelect2')
        });
    });
</script>
<script src="vendor/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script>
    $('.js-pscroll').each(function(){
        $(this).css('position','relative');
        $(this).css('overflow','hidden');
        var ps = new PerfectScrollbar(this, {
            wheelSpeed: 1,
            scrollingThreshold: 1000,
            wheelPropagation: false,
        });
        $(window).on('resize', function(){
            ps.update();
        });
    });
</script>
<script src="js/main.js"></script>

</body>
</html>
