<?php
include('connection.php');
require 'PHPMailer.php';
require 'Exception.php';
require 'SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if (isset($_POST['update_tracking'])) {
    $order_id = mysqli_real_escape_string($con, $_POST['order_id']);
    $order_status = mysqli_real_escape_string($con, $_POST['order_status']);
    $tracking_number = mysqli_real_escape_string($con, $_POST['tracking_number']);

    $update_query = "UPDATE completed_orders SET order_status = '$order_status', tracking_number = '$tracking_number' WHERE id = '$order_id'";
    mysqli_query($con, $update_query);

    // Fetch user data
    $order_query = mysqli_query($con, "SELECT * FROM completed_orders WHERE id = '$order_id'");
    if (mysqli_num_rows($order_query) > 0) {
        $order = mysqli_fetch_assoc($order_query);

        $to = $order['email'];
        $subject = "Your Order has been Updated - Tracking Info Included";
        $message = "<h3>Hi " . $order['uname'] . ",</h3>";
        $message .= "<p>Your order status has been updated to <strong>" . $order_status . "</strong>.</p>";
        $message .= "<p><strong>Tracking Number:</strong> " . $tracking_number . "</p>";
        $message .= "<p><strong>Product:</strong> " . $order['proname'] . "</p>";
        $message .= "<p><strong>Quantity:</strong> " . $order['proqty'] . "</p>";
        $message .= "<p><strong>Total Price:</strong> " . $order['total_amount'] . " PKR</p>";
        $message .= "<p>You can <a href='https://yourdomain.com/track_order.php'>track your order</a> using your tracking number.</p>";
        $message .= "<p>Thank you for shopping with us!</p>";

        // Send email via SMTP
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'jawadulbahar@gmail.com'; // Your Gmail
            $mail->Password   = 'delwgxougwyvdpzi'; // App password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('jawadulbahar@gmail.com', 'ZUFÉ');
            $mail->addAddress($to, $order['uname']);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;

            $mail->send();
        } catch (Exception $e) {
            error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    }

    echo "<script>location.href = location.href;</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Completed Orders - Admin Panel</title>

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
</head>

<body>
<div id="wrapper">

    <?php include("aside.php"); ?>

    <div style="margin-top: 100px; margin-left: 125px;" class="container mb-5">
        <h1 class="text-center">
            Completed <span style="color:#28a745">Orders ✔</span>
        </h1>

        <div class="row">
            <div class="table-responsive">
                <table id="completedOrdersTable" class="table table-bordered table-striped">
                    <thead style="background-color: #28a745;" class="text-white">
                        <tr>
                            <th>Image</th>
                            <th>Prod_ID</th>
                            <th>Prod_Name</th>
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
                            <th>Status</th>
                            <th>Completed At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $completedQ = mysqli_query($con, "
                            SELECT c.*, p.name AS product_name
                            FROM completed_orders c
                            JOIN products p ON c.product_id = p.id
                            ORDER BY c.tracking_number, c.completed_at DESC
                        ");
                        $completedGrouped = [];
                        while ($row = mysqli_fetch_assoc($completedQ)) {
                            $tracking = $row['tracking_number'];
                            if (!isset($completedGrouped[$tracking])) {
                                $completedGrouped[$tracking] = [];
                            }
                            $completedGrouped[$tracking][] = $row;
                        }

                            $first = true;

                            foreach ($completedGrouped as $tracking => $orders) {
                            $first = $orders[0]; // shared info
                            $productHTML = $imageHTML = $qtyHTML = $priceHTML = $colorHTML = $sizeHTML = $typeHTML = $taxHTML = $totalHTML = '';

                            foreach ($orders as $order) {
                                $productHTML .= "<div><strong>{$order['proname']}</strong></div>";
                                $imageHTML .= "<img src='../adminpanel3/img/".htmlspecialchars($order['product_img'])."' width='60' height='70' class='rounded mr-1 mb-1'><br>";
                                $qtyHTML .= "<div>{$order['proqty']}</div>";
                                $priceHTML .= "<div>{$order['proprice']} PKR</div>";
                                $colorHTML .= "<div>{$order['selected_color']}</div>";
                                $sizeHTML .= "<div>{$order['selected_size']}</div>";
                                $typeHTML .= "<div>{$order['shirt_type']}</div>";
                                $taxHTML .= "<div>{$order['item_tax']} PKR</div>";
                                $totalHTML .= "<div>{$order['total_amount']} PKR</div>";
                            }

                            // Subtotal calculation
                            $subtotal = 0;
                            foreach ($orders as $o) {
                                $subtotal += $o['total_amount'];
                            }
                        ?>
                            <tr>
                                <td><?= $imageHTML; ?></td>
                                <td><?= implode('<hr>', array_column($orders, 'product_id')); ?></td>
                                <td><?= $productHTML; ?></td>
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
                                <td><?= $first['shipping_charges'] ?? '0'; ?> PKR</td>
                                <td><?= $taxHTML; ?></td>
                                <td><?= $totalHTML; ?></td>
                                <td><strong><?= number_format($subtotal); ?> PKR</strong></td>
                                <td><span class="badge badge-success"><?= $tracking; ?></span></td>
                                <td><?= $first['order_status']; ?></td>
                                <td><?= date('d M Y, h:i A', strtotime($first['completed_at'])); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#statusModal<?= $first['id']; ?>">Update</button>

                                    <!-- Modal -->
                                    <div class="modal fade" id="statusModal<?= $first['id']; ?>" tabindex="-1" role="dialog">
                                        <div class="modal-dialog" role="document">
                                            <form method="post">
                                                <input type="hidden" name="order_id" value="<?= $first['id']; ?>">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Update Order</h5>
                                                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label>Order Status:</label>
                                                            <select class="form-control" name="order_status" required>
                                                                <option value="Processing" <?= $first['order_status'] == 'Processing' ? 'selected' : '' ?>>Processing</option>
                                                                <option value="Shipped" <?= $first['order_status'] == 'Shipped' ? 'selected' : '' ?>>Shipped</option>
                                                                <option value="Out for Delivery" <?= $first['order_status'] == 'Out for Delivery' ? 'selected' : '' ?>>Out for Delivery</option>
                                                                <option value="Delivered" <?= $first['order_status'] == 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Tracking Number:</label>
                                                            <input type="text" name="tracking_number" class="form-control" value="<?= $tracking; ?>" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" name="update_tracking" class="btn btn-success">Save</button>
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                            <?php
                                $first = false;
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
        $('#completedOrdersTable').DataTable({
            pageLength: 10,
            ordering: true,
            responsive: true
        });
    });
</script>

</body>
</html>