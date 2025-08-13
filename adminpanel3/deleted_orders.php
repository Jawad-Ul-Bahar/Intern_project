<?php
include('connection.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Deleted Orders - Admin Panel</title>
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
        <h2 class="text-center">Deleted Orders</h2>

        <div class="row">
            <div class="table-responsive">
                <table id="deletedOrdersTable" class="table table-bordered table-striped">
                    <thead style="background-color: #dc3545;" class="text-white">
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
                        <th>Tracking #</th>
                        <th>Cancel Reason</th>
                        <th>Status</th>
                        <th>Order At</th>
                        <th>Deleted At</th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php
                    $q = mysqli_query($con, "
                        SELECT b.*, p.name AS product_name 
                        FROM all_orders_backup b
                        LEFT JOIN products p ON b.product_id = p.id
                        ORDER BY b.deleted_at DESC
                    ");

                    while ($data = mysqli_fetch_array($q)) {
                    ?>
                    <tr>
                        <td><?= $data['product_name'] ?? 'N/A'; ?></td>
                        <td><img src="../adminpanel3/img/<?= htmlspecialchars($data['product_img']); ?>" width="60" height="70" class="rounded"></td>
                        <td><?= $data['proqty']; ?></td>
                        <td><?= $data['proprice']; ?> PKR</td>
                        <td><?= $data['selected_color'] ?? '-'; ?></td>
                        <td><?= $data['selected_size'] ?? '-'; ?></td>
                        <td><?= $data['shirt_type'] ?? '-'; ?></td>
                        <td><?= $data['uname']; ?></td>
                        <td><?= $data['email']; ?></td>
                        <td><?= $data['work_phone']; ?></td>
                        <td><?= $data['home_phone'] ?? '-'; ?></td>
                        <td><?= $data['delivery_area'] ?? '-'; ?></td>
                        <td><small><?= $data['address']; ?></small></td>
                        <td><?= $data['city'] ?? '-'; ?></td>
                        <td><?= $data['country'] ?? '-'; ?></td>
                        <td><?= $data['postal_code'] ?? '-'; ?></td>
                        <td><?= $data['shipping_charges'] ?? '0'; ?> PKR</td>
                        <td><?= $data['item_tax'] ?? '0'; ?> PKR</td>
                        <td><?= $data['total_amount'] ?? '0'; ?> PKR</td>
                        <td><span class="badge badge-info"><?= $data['tracking_number'] ?? 'N/A'; ?></span></td>
                        <td><?= $data['cancel_reason'] ?? '-'; ?></td>
                        <td><span class="badge badge-danger">Cancelled</span></td>
                        <td><?= date('d M Y, h:i A', strtotime($data['order_time'])); ?></td>
                        <td><?= date('d M Y, h:i A', strtotime($data['deleted_at'])); ?></td>
                    </tr>
                    <?php } ?>

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
    $(document).ready(function () {
        $('#deletedOrdersTable').DataTable({
            pageLength: 10,
            ordering: true,
            responsive: true
        });
    });
</script>
</body>
</html>