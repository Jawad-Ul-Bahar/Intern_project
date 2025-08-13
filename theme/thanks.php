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

// Get tracking number from URL, or use session data, or show empty state
$tracking_number = $_GET['tracking_number'] ?? '';
$show_no_orders = false;
$orders = [];
$first_order = null;
$is_cancelled_order = false;
$is_admin_cancelled = false; // ✅ NEW: Track admin cancellations
$order_source = ''; // Track which table the order came from

// Try to get tracking number from session if not in URL
if (empty($tracking_number) && isset($_SESSION['last_order_tracking'])) {
    $tracking_number = $_SESSION['last_order_tracking'];
}

// ✅ UPDATED: If still no tracking number, try to get the latest ACTIVE order for this user
if (empty($tracking_number)) {
    $uid = $_SESSION['userid'] ?? 0;
    $session_id = session_id();
    
    // Try to get the most recent ACTIVE order for logged-in user (skip cancelled/deleted orders)
    if ($uid > 0) {
        // First check completed_orders table for active orders
        $recent_order_q = mysqli_query($con, "SELECT tracking_number FROM completed_orders WHERE uid = '$uid' AND order_status != 'cancelled' ORDER BY id DESC LIMIT 1");
        if ($recent_order_q && mysqli_num_rows($recent_order_q) > 0) {
            $recent_order = mysqli_fetch_assoc($recent_order_q);
            $tracking_number = $recent_order['tracking_number'];
        } else {
            // Then check orders table for pending orders
            $recent_order_q = mysqli_query($con, "SELECT tracking_number FROM orders WHERE uid = '$uid' AND order_status != 'cancelled' ORDER BY id DESC LIMIT 1");
            if ($recent_order_q && mysqli_num_rows($recent_order_q) > 0) {
                $recent_order = mysqli_fetch_assoc($recent_order_q);
                $tracking_number = $recent_order['tracking_number'];
            }
        }
    }
    
    // If still no tracking number and user is guest, try to find by email (only active orders)
    if (empty($tracking_number) && isset($_SESSION['checkout_email'])) {
        $email = $_SESSION['checkout_email'];
        // First check completed_orders table for active orders
        $recent_order_q = mysqli_query($con, "SELECT tracking_number FROM completed_orders WHERE email = '$email' AND order_status != 'cancelled' ORDER BY id DESC LIMIT 1");
        if ($recent_order_q && mysqli_num_rows($recent_order_q) > 0) {
            $recent_order = mysqli_fetch_assoc($recent_order_q);
            $tracking_number = $recent_order['tracking_number'];
        } else {
            // Then check orders table for active orders
            $recent_order_q = mysqli_query($con, "SELECT tracking_number FROM orders WHERE email = '$email' AND order_status != 'cancelled' ORDER BY id DESC LIMIT 1");
            if ($recent_order_q && mysqli_num_rows($recent_order_q) > 0) {
                $recent_order = mysqli_fetch_assoc($recent_order_q);
                $tracking_number = $recent_order['tracking_number'];
            }
        }
    }
}

// ✅ UPDATED: Search in all tables for the tracking number (including deleted_orders)
if (!empty($tracking_number)) {
    // First try completed_orders table (for approved/shipped/delivered orders)
    $order_q = mysqli_query($con, "SELECT * FROM completed_orders WHERE tracking_number = '$tracking_number'");
    if ($order_q && mysqli_num_rows($order_q) > 0) {
        $orders = mysqli_fetch_all($order_q, MYSQLI_ASSOC);
        $order_source = 'completed_orders';
    } else {
        // Then try orders table (for pending orders)
        $order_q = mysqli_query($con, "SELECT * FROM orders WHERE tracking_number = '$tracking_number'");
        if ($order_q && mysqli_num_rows($order_q) > 0) {
            $orders = mysqli_fetch_all($order_q, MYSQLI_ASSOC);
            $order_source = 'orders';
        } else {
            // ✅ NEW: Finally check deleted_orders table (for admin cancelled orders)
            $order_q = mysqli_query($con, "SELECT * FROM all_orders_backup WHERE tracking_number = '$tracking_number'");
            if ($order_q && mysqli_num_rows($order_q) > 0) {
                $orders = mysqli_fetch_all($order_q, MYSQLI_ASSOC);
                $order_source = 'deleted_orders';
                $is_admin_cancelled = true;
                $is_cancelled_order = true;
            } else {
                // ✅ NEW: Also check all_orders_backup table if deleted_orders doesn't exist
                $order_q = mysqli_query($con, "SELECT * FROM all_orders_backup WHERE tracking_number = '$tracking_number'");
                if ($order_q && mysqli_num_rows($order_q) > 0) {
                    $orders = mysqli_fetch_all($order_q, MYSQLI_ASSOC);
                    $order_source = 'all_orders_backup';
                    $is_admin_cancelled = true;
                    $is_cancelled_order = true;
                }
            }
        }
    }
    
    if (!empty($orders)) {
        $first_order = $orders[0];
        $order_id = $first_order['id'];
        
        // ✅ UPDATED: Handle different cancellation scenarios and clear invalid session data
        if ($first_order['order_status'] === 'cancelled' || $is_admin_cancelled) {
            $is_cancelled_order = true;
            $show_no_orders = true;
            // Clear the session tracking for cancelled orders
            unset($_SESSION['last_order_tracking']);
        } else {
            // ✅ Store tracking number in session for future reference (only for active orders)
            $_SESSION['last_order_tracking'] = $tracking_number;
            // ✅ Don't show empty state for non-cancelled orders
            $show_no_orders = false;
        }
    } else {
        // ✅ NEW: If no order found but we have a tracking number in session, 
        // it might be cancelled/deleted, so clear the session
        if (!empty($tracking_number) && isset($_SESSION['last_order_tracking'])) {
            unset($_SESSION['last_order_tracking']);
        }
        $show_no_orders = true;
    }
} else {
    // No tracking number available - show empty state
    $show_no_orders = true;
}

// ✅ UPDATED: Cancel Logic (only works for pending orders in 'orders' table)
$cancel_success = false;
if (!empty($orders) && !$is_cancelled_order && isset($_POST['cancel_order']) && $order_source === 'orders') {
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

        // ✅ After successful cancellation, show empty state
        $show_no_orders = true;
        $is_cancelled_order = true;
        // Clear session tracking
        unset($_SESSION['last_order_tracking']);
    }
}

// ✅ UPDATED: Calculate cancel option and totals (only if orders exist and not cancelled)
$show_cancel = false;
$subtotal = 0;
$shipping = 0;
$item_tax = 0;
$total = 0;

if (!empty($orders) && !$is_cancelled_order) {
    // Only show cancel option for pending orders in 'orders' table within 1 hour
    if ($order_source === 'orders') {
        $order_time = strtotime($first_order['order_time']);
        $now = time();
        $time_diff = ($now - $order_time) / 60;
        $show_cancel = ($time_diff <= 60) && ($first_order['order_status'] === 'pending');
    }

    // Calculate totals
    foreach ($orders as $order) {
        $subtotal += $order['proprice'] * $order['proqty'];
    }
    $shipping = (float) $first_order['shipping_charges'];
    $item_tax = (float) $first_order['item_tax'];
    $total = $subtotal + $shipping + $item_tax;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $show_no_orders ? 'My Orders' : 'Order Details'; ?></title>
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
        .order-details-container {
            background: #f8f9fa;
            min-height: 100vh;
            padding: 60px 0;
        }
        
        .order-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            border-radius: 15px 15px 0 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .order-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="rgba(255,255,255,0.1)"/></svg>') repeat;
            animation: float 20s linear infinite;
        }
        
        @keyframes float {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }
        
        .order-content {
            background: white;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .status-pending { background: #fff3cd; color: #856404; }
        .status-approved { background: #d1ecf1; color: #0c5460; }
        .status-shipped { background: #cce7ff; color: #004085; }
        .status-delivered { background: #d4edda; color: #155724; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
        
        .product-item {
            border: 1px solid #e9ecef;
            border-radius: 12px;
            margin-bottom: 20px;
            transition: transform 0.2s, box-shadow 0.2s;
            overflow: hidden;
        }
        
        .product-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        
        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .order-summary-card {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-top: 30px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
        }
        
        .summary-row.total {
            border-top: 2px solid rgba(255,255,255,0.3);
            padding-top: 15px;
            font-size: 1.2em;
            font-weight: bold;
        }
        
        .info-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        
        .cancel-section {
            background: #fff5f5;
            border: 2px dashed #fc8181;
            border-radius: 12px;
            padding: 25px;
            margin-top: 30px;
        }
        
        .btn-cancel {
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            color: white;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-cancel:hover {
            background: linear-gradient(135deg, #ee5a52, #ff6b6b);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(238, 90, 82, 0.4);
            color: white;
        }
        
        .tracking-code {
            background: #f8f9fa;
            border: 2px dashed #6c757d;
            border-radius: 8px;
            padding: 15px;
            font-family: 'Courier New', monospace;
            font-size: 1.1em;
            font-weight: bold;
            text-align: center;
            margin: 15px 0;
            position: relative;
        }
        
        .copy-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: #007bff;
            border: none;
            color: white;
            border-radius: 4px;
            padding: 5px 10px;
            font-size: 12px;
            cursor: pointer;
        }
        
        .alert-custom {
            border-radius: 12px;
            border: none;
            padding: 20px;
            margin-bottom: 25px;
        }
        
        .alert-info-custom {
            background: linear-gradient(135deg, #74b9ff, #0984e3);
            color: white;
        }
        
        .product-details {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
        }
        
        .detail-tag {
            display: inline-block;
            background: #e9ecef;
            color: #495057;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            margin: 2px;
            font-weight: 500;
        }
        
        /* No Orders Styles */
        .no-orders-container {
            text-align: center;
            padding: 80px 30px;
            background: linear-gradient(135deg, #f8f9fa, #ffffff);
        }
        
        .no-orders-icon {
            font-size: 140px;
            color: #e9ecef;
            margin-bottom: 30px;
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }
        
        .no-orders-title {
            color: #495057;
            font-size: 2.5em;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .no-orders-message {
            color: #6c757d;
            font-size: 1.2em;
            margin-bottom: 50px;
            line-height: 1.6;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .btn-continue-shopping {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            border-radius: 30px;
            padding: 18px 45px;
            color: white;
            font-weight: 600;
            font-size: 1.1em;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin: 10px;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        
        .btn-continue-shopping:hover {
            background: linear-gradient(135deg, #764ba2, #667eea);
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
            color: white;
            text-decoration: none;
        }
        
        .btn-track-order {
            background: transparent;
            border: 2px solid #667eea;
            color: #667eea;
            border-radius: 30px;
            padding: 16px 35px;
            font-weight: 600;
            font-size: 1.1em;
            transition: all 0.3s ease;
            margin: 10px;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-track-order:hover {
            background: #667eea;
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }
        
        .empty-state-features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 60px;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .feature-item {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .feature-item:hover {
            transform: translateY(-5px);
        }
        
        .feature-icon {
            font-size: 3em;
            color: #667eea;
            margin-bottom: 20px;
        }
        
        .feature-title {
            font-size: 1.3em;
            font-weight: 600;
            color: #495057;
            margin-bottom: 10px;
        }
        
        .feature-desc {
            color: #6c757d;
            line-height: 1.5;
        }

        /* ✅ UPDATED: Special styles for cancelled order empty state */
        .cancelled-order-message {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 40px;
            text-align: center;
            animation: fadeInDown 0.6s ease-out;
        }
        
        /* ✅ NEW: Special style for admin cancelled orders */
        .admin-cancelled-message {
            background: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%);
            color: white;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 40px;
            text-align: center;
            animation: fadeInDown 0.6s ease-out;
        }
        
        @keyframes fadeInDown {
            0% { opacity: 0; transform: translateY(-20px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        /* ✅ UPDATED: Order source indicator */
        .order-source-badge {
            position: absolute;
            top: 10px;
            right: 15px;
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 5px 12px;
            border-radius: 12px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>

<?php include("header.php"); ?>

<div class="order-details-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                
                <!-- Order Header -->
                <div class="order-header">
                    <?php if (!empty($orders) && !$show_no_orders): ?>
                        <div class="order-source-badge">
                            <?php 
                                if ($order_source === 'completed_orders') {
                                    echo 'Processed Order';
                                } elseif ($order_source === 'deleted_orders' || $order_source === 'all_orders_backup') {
                                    echo 'Cancelled Order';
                                } else {
                                    echo 'New Order';
                                }
                            ?>
                        </div>
                    <?php endif; ?>
                    <h2 class="mb-3">
                        <i class="fas fa-<?php echo $show_no_orders ? 'shopping-bag' : 'receipt'; ?> mr-3"></i>
                        <?php echo $show_no_orders ? 'My Orders' : 'Order Details'; ?>
                    </h2>
                    <p class="mb-0">
                        <?php echo $show_no_orders ? 'Your order history and shopping hub' : 'Track and manage your order'; ?>
                    </p>
                </div>
                
                <!-- Order Content -->
                <div class="order-content">
                    
                    <?php if ($show_no_orders): ?>
                        <!-- No Orders Section (Only for cancelled or empty orders) -->
                        <div class="no-orders-container">
                            
                            <!-- ✅ UPDATED: Different messages for different cancellation scenarios -->
                            <?php if ($cancel_success): ?>
                                <!-- Customer cancelled order -->
                                <div class="cancelled-order-message">
                                    <h4><i class="fas fa-check-circle mr-2"></i>Order Successfully Cancelled</h4>
                                    <p class="mb-0">Your order has been cancelled and you'll receive a confirmation email shortly. Thank you for shopping with us!</p>
                                </div>
                            <?php elseif ($is_admin_cancelled): ?>
                                <!-- Admin cancelled order -->
                                <div class="admin-cancelled-message">
                                    <h4><i class="fas fa-exclamation-triangle mr-2"></i>Order Cancelled by Admin</h4>
                                    <p class="mb-0">This order has been cancelled by our admin team. If you have any questions or concerns, please contact our customer support. We're sorry for any inconvenience.</p>
                                </div>
                            <?php elseif ($is_cancelled_order): ?>
                                <!-- General cancelled order -->
                                <div class="cancelled-order-message">
                                    <h4><i class="fas fa-times-circle mr-2"></i>Order Cancelled</h4>
                                    <p class="mb-0">This order has been cancelled. If you have any questions, please contact our customer support team.</p>
                                </div>
                            <?php endif; ?>
                            
                            <div class="no-orders-icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <h3 class="no-orders-title">
                                <?php 
                                    if ($cancel_success || $is_cancelled_order) {
                                        echo 'Ready for Your Next Order?';
                                    } else {
                                        echo 'Your Order List is Empty';
                                    }
                                ?>
                            </h3>
                            <p class="no-orders-message">
                                <?php if ($cancel_success): ?>
                                    Your order has been successfully cancelled. Don't worry, you can always place a new order! 
                                    Start exploring our amazing collection of products and discover something perfect for you.
                                <?php elseif ($is_admin_cancelled): ?>
                                    This order was cancelled by our admin team. This might be due to stock availability, payment issues, 
                                    or other administrative reasons. Please feel free to place a new order or contact support for assistance.
                                <?php elseif ($is_cancelled_order): ?>
                                    This order has been cancelled. You can always place a new order! 
                                    Start exploring our amazing collection of products and discover something perfect for you.
                                <?php else: ?>
                                    You haven't placed any orders yet. Start exploring our amazing collection of products 
                                    and discover something perfect for you. Your shopping journey begins here!
                                <?php endif; ?>
                            </p>
                            
                            <div class="action-buttons">
                                <a href="index.php" class="btn-continue-shopping">
                                    <i class="fas fa-shopping-bag mr-2"></i>Continue Shopping
                                </a>
                                <a href="track_order.php" class="btn-track-order">
                                    <i class="fas fa-search mr-2"></i>Track Another Order
                                </a>
                                <?php if ($is_admin_cancelled): ?>
                                    <br><br>
                                    <a href="mailto:support@yourstore.com" class="btn-track-order">
                                        <i class="fas fa-headset mr-2"></i>Contact Support
                                    </a>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Feature Cards -->
                            <div class="empty-state-features">
                                <div class="feature-item">
                                    <div class="feature-icon">
                                        <i class="fas fa-truck"></i>
                                    </div>
                                    <h4 class="feature-title">Fast Delivery</h4>
                                    <p class="feature-desc">Quick and reliable shipping to your doorstep with real-time tracking.</p>
                                </div>
                                
                                <div class="feature-item">
                                    <div class="feature-icon">
                                        <i class="fas fa-shield-alt"></i>
                                    </div>
                                    <h4 class="feature-title">Secure Shopping</h4>
                                    <p class="feature-desc">Safe and secure payment methods with buyer protection guarantee.</p>
                                </div>
                            </div>
                        </div>
                        
                    <?php else: ?>
                        <!-- ✅ Orders Exist Section - SHOWS FOR ALL NON-CANCELLED ORDERS -->
                        <div class="p-4">

                            <!-- Order Info Cards -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <h5><i class="fas fa-info-circle text-primary mr-2"></i>Order Status</h5>
                                        <span class="status-badge status-<?= $first_order['order_status'] ?>">
                                            <?= ucfirst($first_order['order_status']) ?>
                                        </span>
                                        <div class="tracking-code mt-3">
                                            <?= $first_order['tracking_number'] ?>
                                            <button class="copy-btn" onclick="copyTracking()">Copy</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <h5><i class="fas fa-map-marker-alt text-success mr-2"></i>Delivery Info</h5>
                                        <p class="mb-2"><strong>Area:</strong> <?= $first_order['delivery_area'] ?></p>
                                        <p class="mb-2"><strong>Customer:</strong> <?= $first_order['uname'] ?></p>
                                        <p class="mb-0"><strong>Phone:</strong> <?= $first_order['work_phone'] ?></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Products Section -->
                            <h4 class="mb-4"><i class="fas fa-shopping-bag text-primary mr-2"></i>Ordered Items</h4>
                            
                            <?php foreach ($orders as $index => $order): ?>
                                <div class="product-item">
                                    <div class="row align-items-center p-3">
                                        <div class="col-md-3 text-center mb-3 mb-md-0">
                                            <img src="../adminpanel3/img/<?= $order['product_img'] ?>" 
                                                 alt="Product Image" class="product-image">
                                        </div>
                                        <div class="col-md-9">
                                            <h5 class="text-primary mb-3"><?= $order['proname'] ?></h5>
                                            
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <p class="mb-2"><strong>Price:</strong> 
                                                        <span class="text-success">Rs. <?= number_format($order['proprice']) ?></span>
                                                    </p>
                                                    <p class="mb-2"><strong>Quantity:</strong> 
                                                        <span class="badge badge-secondary"><?= $order['proqty'] ?></span>
                                                    </p>
                                                </div>
                                                <div class="col-sm-6">
                                                    <p class="mb-2"><strong>Subtotal:</strong> 
                                                        <span class="text-primary font-weight-bold">
                                                            Rs. <?= number_format($order['proprice'] * $order['proqty']) ?>
                                                        </span>
                                                    </p>
                                                </div>
                                            </div>
                                            
                                            <div class="product-details">
                                                <?php if (!empty($order['selected_color'])): ?>
                                                    <span class="detail-tag">
                                                        <i class="fas fa-palette mr-1"></i><?= $order['selected_color'] ?>
                                                    </span>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($order['selected_size'])): ?>
                                                    <span class="detail-tag">
                                                        <i class="fas fa-ruler mr-1"></i><?= $order['selected_size'] ?>
                                                    </span>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($order['shirt_type'])): ?>
                                                    <span class="detail-tag">
                                                        <i class="fas fa-tshirt mr-1"></i><?= $order['shirt_type'] ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <?php
                            $item_tax = isset($item_tax) ? $item_tax : 0;
                            $total = $subtotal + $shipping + $item_tax;
                            ?>

                            <!-- Order Summary -->
                            <div class="order-summary-card">
                                <h4 class="mb-4"><i class="fas fa-calculator mr-2"></i>Order Summary</h4>
                                
                                <div class="summary-row">
                                    <span>Items Subtotal:</span>
                                    <span>Rs. <?= number_format($subtotal, 2) ?></span>
                                </div>
                                
                                <div class="summary-row">
                                    <span>Shipping Charges:</span>
                                    <span>Rs. <?= number_format($shipping, 2) ?></span>
                                </div>
                                
                                <?php if ($item_tax > 0): ?>
                                    <div class="summary-row">
                                        <span>Tax:</span>
                                        <span>Rs. <?= number_format($item_tax, 2) ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="summary-row total">
                                    <span>Total Amount:</span>
                                    <span>Rs. <?= number_format($total, 2) ?></span>
                                </div>
                            </div>

                            <!-- Order Status Info -->
                            <div class="alert alert-info-custom alert-custom mt-4">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h5 class="mb-2"><i class="fas fa-info-circle mr-2"></i>Order Status Information</h5>
                                        <p class="mb-0">
                                            <?php if ($first_order['order_status'] === 'pending'): ?>
                                                Your order is currently being reviewed. You'll receive a confirmation once approved.
                                            <?php elseif ($first_order['order_status'] === 'approved'): ?>
                                                Great! Your order has been approved and is being prepared for shipment.
                                            <?php elseif ($first_order['order_status'] === 'shipped'): ?>
                                                Your order is on its way! Track your package for delivery updates.
                                            <?php elseif ($first_order['order_status'] === 'delivered'): ?>
                                                Congratulations! Your order has been successfully delivered.
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <a href="track_order.php" class="btn btn-light btn-sm">
                                            <i class="fas fa-search mr-2"></i>Track Another Order
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Cancel Order Section (Only for pending orders in 'orders' table) -->
                            <?php if ($show_cancel): ?>
                                <div class="cancel-section">
                                    <h5 class="text-danger mb-3">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>Cancel Order
                                    </h5>
                                    <p class="text-muted mb-3">
                                        You can cancel this order within 1 hour of placing it. After cancellation, 
                                        you'll receive a confirmation email.
                                    </p>
                                    
                                    <form method="POST" id="cancelForm">
                                        <button type="button" id="cancelBtn" class="btn btn-cancel">
                                            <i class="fas fa-times mr-2"></i>Cancel This Order
                                        </button>

                                        <div id="cancelReasonWrap" style="display: none;" class="mt-4">
                                            <div class="form-group">
                                                <label for="cancel_reason" class="font-weight-bold">
                                                    <i class="fas fa-comment mr-2"></i>Why are you cancelling this order?
                                                </label>
                                                <textarea name="cancel_reason" id="cancel_reason" class="form-control" 
                                                          rows="4" placeholder="Please provide a reason for cancellation..."></textarea>
                                            </div>
                                            <button type="submit" name="cancel_order" id="confirmCancelBtn" 
                                                    class="btn btn-danger w-100" disabled>
                                                <i class="fas fa-check mr-2"></i>Confirm Cancellation
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            <?php endif; ?>

                        </div>
                    <?php endif; ?>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("footer.php"); ?>

<script>
    function copyTracking() {
        <?php if (!empty($first_order)): ?>
        const trackingCode = '<?= $first_order['tracking_number'] ?>';
        navigator.clipboard.writeText(trackingCode).then(() => {
            const btn = event.target;
            const originalText = btn.textContent;
            btn.textContent = 'Copied!';
            btn.style.background = '#28a745';
            
            setTimeout(() => {
                btn.textContent = originalText;
                btn.style.background = '#007bff';
            }, 2000);
        });
        <?php endif; ?>
    }

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