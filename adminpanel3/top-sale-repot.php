    <?php 
    include('connection.php'); 

    // Ensure only admin can access

    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
        <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">
        <title>Sales Performance Report</title>

        <!-- Fonts and Icons -->
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">

        <!-- Bootstrap & DataTables -->
        <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">

        <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
        
        <!-- Custom fonts for this template-->
        <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
        <link
            href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
            rel="stylesheet">

        <!-- Custom styles for this template-->
        <link href="css/sb-admin-2.min.css" rel="stylesheet">


        <style>
            html, body {
                height: 100%;
                width: 90%;
                margin-left: 120px;
                padding: 0;
                display: flex;
                flex-direction: column;
            }
            .main-wrapper { flex: 1; display: flex; flex-direction: column; }
            .container { flex: 1; }
            footer {
                background: #333;
                color: white;
                text-align: center;
                padding: 10px;
                position: relative;
                bottom: 0;
                width: 100%;
            }
            /* DataTables customization */
            .dataTables_wrapper .dataTables_paginate .paginate_button.current {
                background: #009efb !important;
                color: white !important;
                border: none;
            }
            .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
                background: #007bff !important;
                color: white !important;
            }
            .dataTables_wrapper .dataTables_filter { float: right; margin-bottom: 10px; }
            .dataTables_wrapper .dataTables_length { float: left; margin-bottom: 10px; }
            #searchBox {
                border: 1px solid #ddd;
                padding: 8px;
                border-radius: 4px;
            }
        </style>
    </head>

    <body>

    <?php include('aside.php'); ?>

        <div class="main-wrapper">

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
        </div>
        

        <?php include('footer.php'); ?>

        <script>
        $(document).ready(function() {
            // Initialize DataTables
            $('#productsTable, #clientsTable').DataTable({
                pageLength: 10,
                ordering: true,
                responsive: true
            });

            // Custom search box
            $('#searchBox').on('keyup', function() {
                $('#productsTable').DataTable().search(this.value).draw();
                $('#clientsTable').DataTable().search(this.value).draw();
            });
        });
        </script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/chart-area-demo.js"></script>
    </body>
    </html>
