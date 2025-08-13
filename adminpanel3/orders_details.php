<?php
include('connection.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer.php';
require 'SMTP.php';
require 'Exception.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Order History - Admin Panel</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
</head>

<body id="page-top">
<div id="wrapper">

    <?php include("aside.php"); ?>

    <div style="margin-top: 100px; margin-left: 125px" class="container mb-5">
        <h2 class="text-center">Orders Detail</h2>

        <div class="row">
            <div class="table-responsive">
                <table id="ordersTable" class="table table-bordered table-striped">
                   <thead style="background-color: #009efb;" class="text-white">
    <tr>
        <th>Product</th>
        <th>Image</th>
        <th>Qty</th>
        <th>Price</th>
        <th>Color</th>
        <th>Size</th>
        <th>Shirt Type</th>
        <th>Customer</th>
        <th>Email</th>
        <th>Work Phone</th>
        <th>Home Phone</th>
        <th>Area</th>
        <th>Address</th>
        <th>City</th>
        <th>Country</th>
        <th>Postal Code</th>
        <th>Shipping</th>
        <th>Tax</th>
        <th>Total</th>
        <th>Subtotal</th>
        <th>Tracking #</th>
        <th>Cancel Reason</th>
        <th>Order At</th>
        <th>Action</th>
    </tr>
</thead>
                    <tbody>

                    <?php
                  $ordersQ = mysqli_query($con, "
    SELECT o.*, p.name AS product_name 
    FROM orders o 
    JOIN products p ON o.product_id = p.id 
    ORDER BY o.tracking_number, o.order_time DESC
");

$ordersGrouped = [];
while ($row = mysqli_fetch_assoc($ordersQ)) {
    $tracking = $row['tracking_number'];
    if (!isset($ordersGrouped[$tracking])) {
        $ordersGrouped[$tracking] = [];
    }
    $ordersGrouped[$tracking][] = $row;
}


                    foreach ($ordersGrouped as $tracking => $orders) {
    $first = $orders[0]; // Use for shared values
$productHTML = $imageHTML = $qtyHTML = $priceHTML = $colorHTML = $sizeHTML = $typeHTML = '';
$shippingHTML = $taxHTML = $totalHTML = ''; // 👈 add these

    foreach ($orders as $order) {
    $productHTML .= "<div><strong>{$order['product_name']}</strong></div>";
    $imageHTML .= "<img src='../adminpanel3/img/".htmlspecialchars($order['product_img'])."' width='60' height='70' class='rounded mr-1 mb-1'><br>";
    $qtyHTML .= "<div>{$order['proqty']}</div>";
    $priceHTML .= "<div>{$order['proprice']} PKR</div>";
    $colorHTML .= "<div>{$order['selected_color']}</div>";
    $sizeHTML .= "<div>{$order['selected_size']}</div>";
    $typeHTML .= "<div>{$order['shirt_type']}</div>";

    // ✅ Show shipping only once per tracking
    if (empty($shippingHTML)) {
        $shippingHTML = "{$order['shipping_charges']} PKR";
    }

    // ✅ Keep these per product
    $taxHTML .= "<div>{$order['item_tax']} PKR</div>";
    $totalHTML .= "<div>{$order['total_amount']} PKR</div>";
}


?>
<tr>
    <td><?= $productHTML; ?></td>
    <td><?= $imageHTML; ?></td>
    <td><?= $qtyHTML; ?></td>
    <td><?= $priceHTML; ?></td>
    <td><?= $colorHTML; ?></td>
    <td><?= $sizeHTML; ?></td>
    <td><?= $typeHTML; ?></td>
    <td><?= $first['uname']; ?></td>
    <td><?= $first['email']; ?></td>
    <td><?= $first['work_phone']; ?></td>
    <td><?= $first['home_phone'] ?? '-'; ?></td>
    <td><?= $first['delivery_area'] ?? '-'; ?></td>
    <td><small><?= $first['address']; ?></small></td>
    <td><?= $first['city'] ?? '-'; ?></td>
    <td><?= $first['country'] ?? '-'; ?></td>
    <td><?= $first['postal_code'] ?? '-'; ?></td>
    <td><?= $shippingHTML; ?></td>
<td><?= $taxHTML; ?></td>
<td><?= $totalHTML; ?></td>
  <td>
        <strong>
            <?php
                $subtotal = 0;
                foreach ($orders as $order) {
                    $subtotal += ($order['proqty'] * $order['proprice']) + $order['item_tax'] + $order['shipping_charges'];
                }
                echo number_format($subtotal) . " PKR";
            ?>
        </strong>
    </td>

    <td><span class="badge badge-info"><?= $tracking ?? 'N/A'; ?></span></td>
    <td><?= $first['cancel_reason'] ?? '-'; ?></td>
    <td><?= date('d M Y, h:i A', strtotime($first['order_time'])); ?></td>
    <td>
        <?php if (strtolower($first['order_status']) === 'cancelled'): ?>
            <a href="?id=<?= $first['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this cancelled order?');">
                <i class="fas fa-trash-alt"></i>
            </a>
            <span class="badge badge-secondary">Cancelled</span>
        <?php else: ?>
            <a href="?id=<?= $first['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this order?');">
                <i class="fas fa-trash-alt"></i>
            </a>
            <a href="?done_id=<?= $first['id']; ?>" class="btn btn-success btn-sm" onclick="return confirm('Mark this order as Done?');">
                <i class="fas fa-check"></i>
            </a>
        <?php endif; ?>
    </td>
</tr>
                <?php } ?>

                                    <?php
                                if (isset($_GET["id"])) {
                    $delid = $_GET["id"];

                    $getOrder = mysqli_query($con, "SELECT * FROM orders WHERE id='$delid'");
                    $order = mysqli_fetch_assoc($getOrder);

                    if ($order) {
                        $userEmail = $order['email'];
                        $userName = $order['uname'];
                        $tracking = $order['tracking_number'];
                        $orderStatus = strtolower($order['order_status']);
                        $productName = $order['product_id']; // If you want actual product name, join with `products` table
                        $userPhone = $order['work_phone'];

                        // Admin contact info
                        $adminEmail = "jawadalbahar@gmail.com";
                        $adminPhone = "+92-3021357103";

                        $mail = new PHPMailer(true);
                        try {
                            $mail->isSMTP();
                            $mail->Host = 'smtp.gmail.com';
                            $mail->SMTPAuth = true;
                            $mail->Username = 'jawadulbahar@gmail.com';
                            $mail->Password = 'delwgxougwyvdpzi';
                            $mail->SMTPSecure = 'tls';
                            $mail->Port = 587;

                            $mail->setFrom('jawadulbahar@gmail.com', 'Order Support');
                            $mail->addAddress($userEmail, $userName);
                            $mail->isHTML(true);

                            if ($orderStatus === 'cancelled') {
                                $mail->Subject = 'Your cancelled order has been removed';
                                $mail->Body = "
                                    <h3>Order Cancelled</h3>
                                    <p>Hello <strong>$userName</strong>,</p>
                                    <p>Your cancelled order <strong>$tracking</strong> has now been removed from our system.</p>
                                    <p>No further action is required from your side.</p>
                                    <p>Thank you,<br>Support Team</p>
                                ";
                            } else {
                                $mail->Subject = 'Your order has been cancelled by admin';
                                $mail->Body = "
                                    <h3>Order Cancelled</h3>
                                    <p>Hello <strong>$userName</strong>,</p>
                                    <p>We regret to inform you that your order <strong>$tracking</strong> has been cancelled by our team due to unforeseen circumstances.</p>
                                    <p>If you have any questions, feel free to reach out:</p>
                                    <p><strong>Email:</strong> $adminEmail<br>
                                    <strong>Phone:</strong> $adminPhone</p>
                                    <p>Thank you,<br>Support Team</p>
                                ";
                            }

                            $mail->send();
                        } catch (Exception $e) {
                            // Optionally log or ignore email error
                        }

                        // 👉 Backup order before deletion
                $backupQuery = mysqli_query($con, "
                    INSERT INTO all_orders_backup (
                        id, product_id, product_img, proqty, proprice, selected_color,
                        selected_size, shirt_type, uname, email, work_phone, home_phone,
                        delivery_area, address, city, country, postal_code, shipping_charges, 
                        item_tax, total_amount, tracking_number, order_status, order_time, 
                        cancel_reason, deleted_at
                    )
                    SELECT 
                        id, product_id, product_img, proqty, proprice, selected_color,
                        selected_size, shirt_type, uname, email, work_phone, home_phone,
                        delivery_area, address, city, country, postal_code, shipping_charges, 
                        item_tax, total_amount, tracking_number, order_status, order_time, 
                        cancel_reason, NOW()
                    FROM orders 
                    WHERE id='$delid'
                ");


                        if ($backupQuery) {
                            // Delete after backup
                            $delq = mysqli_query($con, "DELETE FROM `orders` WHERE id='$delid'");
                            if ($delq) {
                                echo "<script>alert('Order deleted and backed up successfully'); location.assign('orders_details.php');</script>";
                            } else {
                                echo "<script>alert('Delete failed.');</script>";
                            }
                        } else {
                            echo "<script>alert('Backup failed. Order was not deleted.');</script>";
                        }
                    }
                }



                                    if (isset($_GET["done_id"])) {
                    $doneId = $_GET["done_id"];

                    // Get tracking number from one product
                    $getTracking = mysqli_query($con, "SELECT tracking_number FROM orders WHERE id='$doneId'");
                    $trackRow = mysqli_fetch_assoc($getTracking);
                    $trackingNumber = $trackRow['tracking_number'] ?? '';

                    if ($trackingNumber) {
                        // Get all orders for this tracking number
                        $getOrders = mysqli_query($con, "
                            SELECT o.*, p.name AS proname 
                            FROM orders o
                            JOIN products p ON o.product_id = p.id
                            WHERE o.tracking_number = '$trackingNumber'
                        ");

                        $allValid = true;
                        while ($orderData = mysqli_fetch_assoc($getOrders)) {
                            // Skip if cancelled
                            if (strtolower($orderData['order_status']) === 'cancelled') {
                                continue;
                            }

                            $insert = mysqli_query($con, "
                                INSERT INTO completed_orders 
                                (product_id, proname, product_img, proqty, proprice, selected_color, selected_size, shirt_type, uname, email, work_phone, home_phone, delivery_area, address, city, country, postal_code, shipping_charges, item_tax, total_amount, tracking_number, order_time, completed_at)
                                VALUES (
                                    '{$orderData['product_id']}', '{$orderData['proname']}', '{$orderData['product_img']}', '{$orderData['proqty']}', '{$orderData['proprice']}',
                                    '{$orderData['selected_color']}', '{$orderData['selected_size']}', '{$orderData['shirt_type']}',
                                    '{$orderData['uname']}', '{$orderData['email']}', '{$orderData['work_phone']}', '{$orderData['home_phone']}',
                                    '{$orderData['delivery_area']}', '{$orderData['address']}', '{$orderData['city']}', '{$orderData['country']}', '{$orderData['postal_code']}', '{$orderData['shipping_charges']}',
                                    '{$orderData['item_tax']}', '{$orderData['total_amount']}', '{$orderData['tracking_number']}',
                                    '{$orderData['order_time']}', NOW()
                                )
                            ");

                            if (!$insert) {
                                $allValid = false;
                            }
                        }

                        // Only delete if all inserted
                        if ($allValid) {
                            // Update sales report
                            mysqli_query($con, "
                                INSERT INTO sales_reports (
                                    product_id, product_name, total_quantity_sold, total_product_revenue,
                                    total_tax, total_shipping, grand_total, last_completed_at
                                )
                                SELECT 
                                    p.id, p.name, 
                                    SUM(c.proqty), 
                                    SUM(c.proqty * c.proprice), 
                                    SUM(c.item_tax), 
                                    SUM(c.shipping_charges), 
                                    SUM(c.total_amount), 
                                    MAX(c.completed_at)
                                FROM completed_orders c
                                JOIN products p ON c.product_id = p.id
                                GROUP BY c.product_id
                                ON DUPLICATE KEY UPDATE 
                                    total_quantity_sold = VALUES(total_quantity_sold),
                                    total_product_revenue = VALUES(total_product_revenue),
                                    total_tax = VALUES(total_tax),
                                    total_shipping = VALUES(total_shipping),
                                    grand_total = VALUES(grand_total),
                                    last_completed_at = VALUES(last_completed_at),
                                    generated_at = CURRENT_TIMESTAMP
                            ");

                            // Delete all for this tracking number
                            mysqli_query($con, "DELETE FROM orders WHERE tracking_number = '$trackingNumber'");
                            echo "<script>location.href='complete_order.php?track={$trackingNumber}';</script>";
                        } else {
                            echo "<script>alert('Some entries failed.');</script>";
                        }
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

    <?php include("footer.php"); ?>
</div>

<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>

<script>
    $(document).ready(function() {
        $('#ordersTable').DataTable({
            pageLength: 10,
            ordering: true,
            responsive: true
        });
    });
</script>
</body>
</html>