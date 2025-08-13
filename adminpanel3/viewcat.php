<?php 
// Include database connection
include("connection.php")
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags and title -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>SB Admin 2 - Dashboard</title>

    <!-- Font Awesome & Google Fonts -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">

    <!-- SB Admin 2 CSS -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <!-- Page Wrapper Start -->
    <div id="wrapper">

        <!-- Sidebar Include -->
        <?php include("aside.php"); ?>

        <!-- Main Content Start -->
        <div class="container">


            <!-- Category Table -->
             <h2 class="text-left text-dark mb-4">Categories</h2>
            <table class="table table-striped table-inverse table-responsive">
                <thead class="thead-inverse">
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Image</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                // Fetch all categories from database
                $q = mysqli_query($con, "SELECT * FROM categories");
                while($cat = mysqli_fetch_array($q)){
                ?>
                    <tr>
                        <td scope="row"><?php echo $cat[1]?></td>
                        <td><?php echo $cat[2]?></td>
                        <td><img src="img/<?php echo $cat[3]?>" alt="" height="150px"></td>
                        <td>
                            <!-- Delete and Update buttons -->
                            <a href="?did=<?php echo $cat[0]?>" class="btn btn-danger">Delete</a>  
                            <a href="catupdate.php?up=<?php echo $cat[0]?>"  class="btn btn-info">Update</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>

        <?php
        // Handle category deletion
        if (isset($_GET["did"])) {
            $delid = $_GET["did"];
            $delq = mysqli_query($con, "DELETE FROM `categories` WHERE id='$delid'");
            if ($delq) {
                echo "<script> 
                        alert('Delete successfully');
                        location.assign('viewcat.php');
                      </script>";
            }
        }
        ?>

        <!-- Footer Include -->
        <?php include("footer.php") ?>

    </div>
    <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    Select "Logout" below if you are ready to end your current session.
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="login.html">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- JS Files -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Optional Chart Scripts -->
    <script src="vendor/chart.js/Chart.min.js"></script>
    <script src="js/demo/chart-area-demo.js"></script>
    <script src="js/demo/chart-pie-demo.js"></script>

</body>
</html>
