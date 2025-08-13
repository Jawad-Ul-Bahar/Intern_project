<?php include('connection.php'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Sales Report - Admin Panel</title>

    <!-- Required stylesheets -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css" rel="stylesheet">

    <!-- jQuery + DataTables -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
</head>

<body id="page-top">
<div id="wrapper">

    <?php include("aside.php"); ?>

    <!-- Page Content -->
    <div style="margin-top: 100px; margin-left: 125px" class="container mb-5">
        <h2 class="text-center mb-4">
            <span style="color:#009efb">S</span>ales <span style="color:#009efb">R</span>eport (Stored Summary)
        </h2>

        <div class="table-responsive">
            <table id="salesTable" class="table table-bordered table-striped">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>Product Name</th>
                        <th>Qty Sold</th>
                        <th>Product Revenue</th>
                        <th>Tax</th>
                        <th>Shipping</th>
                        <th>Grand Total</th>
                        <th>Last Completed</th>
                        <th>Report Generated</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT * FROM sales_reports ORDER BY total_quantity_sold DESC";
                    $result = mysqli_query($con, $query);

                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['product_name']) . "</td>";
                        echo "<td>" . number_format($row['total_quantity_sold']) . "</td>";
                        echo "<td>Rs. " . number_format($row['total_product_revenue']) . "</td>";
                        echo "<td>Rs. " . number_format($row['total_tax']) . "</td>";
                        echo "<td>Rs. " . number_format($row['total_shipping']) . "</td>";
                        echo "<td>Rs. " . number_format($row['grand_total']) . "</td>";
                        echo "<td>" . date("d-M-Y h:i A", strtotime($row['last_completed_at'])) . "</td>";
                        echo "<td>" . date("d-M-Y h:i A", strtotime($row['generated_at'])) . "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include("footer.php"); ?>
</div>

<!-- JS Scripts -->
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>

<script>
    $(document).ready(function () {
        $('#salesTable').DataTable({
            pageLength: 10,
            ordering: true,
            responsive: true
        });
    });
</script>

</body>
</html>
    