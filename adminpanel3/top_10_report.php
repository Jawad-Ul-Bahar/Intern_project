<?php
include('connection.php')
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>SB Admin 2 - Dashboard</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Fonts and Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">

    <!-- Bootstrap & DataTables -->
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <?php include("aside.php"); ?>

        <!-- Begin Page Content -->
        <div style="margin-top: 100px; margin-left: 125px" class="container mb-5">
            <h1 class="text-center">
                <span style="color:#009efb">S</span>ales <span style="color:#009efb">P</span>erformance Report
            </h1>

            <div class="row">
                <!-- Top Selling Products -->
                <div class="col-xl-6 col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Top Selling Products</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="productsTable" class="table table-bordered table-striped">
                                    <thead class="text-white" style="background-color: #009efb;">
                                        <tr>
                                            <th>Product</th>
                                            <th>Qty Sold</th>
                                            <th>Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT * FROM `product_sales_summary` ORDER BY `total_quantity_sold` DESC LIMIT 10";
                                        $result = mysqli_query($con, $query);

                                        while ($data = mysqli_fetch_assoc($result)) {
                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars($data['proname']) . "</td>";
                                            echo "<td>" . number_format($data['total_quantity_sold']) . "</td>";
                                            echo "<td>" . number_format($data['total_revenue'], 2) . "</td>";
                                            echo "</tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Clients -->
                <div class="col-xl-6 col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Top Clients</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="clientsTable" class="table table-bordered table-striped">
                                    <thead class="text-white" style="background-color: #009efb;">
                                        <tr>
                                            <th>Client</th>
                                            <th>Total Orders</th>
                                            <th>Total Spending</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT * FROM `client_sales_summary` ORDER BY `total_spending` DESC LIMIT 10";
                                        $result = mysqli_query($con, $query);

                                        while ($data = mysqli_fetch_assoc($result)) {
                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars($data['uname']) . "</td>";
                                            echo "<td>" . number_format($data['total_orders']) . "</td>";
                                            echo "<td>$" . number_format($data['total_spending'], 2) . "</td>";
                                            echo "</tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Footer -->
        <?php include("footer.php") ?>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <script>
    $(document).ready(function() {
        // Initialize DataTables
        $('#productsTable, #clientsTable').DataTable({
            pageLength: 10,
            ordering: true,
            responsive: true
        });
    });
    </script>

</body>
</html>
