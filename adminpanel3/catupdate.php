<?php include('connection.php'); // Include DB connection ?>

<?php
// Fetch category details for update form
if (isset($_GET['up'])) {
    $up = $_GET['up'];
    $query = mysqli_query($con, "SELECT * FROM `categories` WHERE id=$up");
    $col = mysqli_fetch_array($query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta & Bootstrap CDN -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Category</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Admin Template CSS -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">

<!-- Page Wrapper -->
<div id="wrapper">

    <!-- Sidebar -->
    <?php include("aside.php"); ?>

    <!-- Page Content -->
    <div class="container-fluid">
        <div class="container mt-5">
            <h2 class="text-center text-primary">Update Record</h2>

            <!-- Update Category Form -->
            <form action="" method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded shadow">
                <div class="mb-3">
                    <label class="form-label">Cat_Name</label>
                    <input type="text" name="name" value="<?php echo $col[1] ?>" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" value="<?php echo $col[2] ?>" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Image</label>
                    <input type="file" name="image" class="form-control" required>
                </div>

                <input class="btn btn-success" type="submit" value="Update" name="update">
                <a href="viewcat.php" class="btn text-white" style="background-color: #4e73df;">View Record</a>
            </form>
        </div>
    </div>

    <?php
    // Update category logic
    if (isset($_POST['update'])) {
        $cname = $_POST['name'];
        $des = $_POST['description'];
        $up = $_GET['up'];

        $imageName = $_FILES['image']['name'];
        $imageTmpName = $_FILES['image']['tmp_name'];
        $destination = "img/" . $imageName;
        $extension = pathinfo($imageName, PATHINFO_EXTENSION);

        if (in_array($extension, ['png', 'jpg', 'jpeg', 'jfif', 'webp'])) {
            if (move_uploaded_file($imageTmpName, $destination)) {
                $query = "UPDATE `categories` SET `cat_name`='$cname', `cat_des`='$des', `image`='$imageName' WHERE `id`='$up'";
                $result = mysqli_query($con, $query);

                if ($result) {
                    echo "<script>alert('Category updated successfully.');</script>";
                } else {
                    echo "<script>alert('Error updating category: " . mysqli_error($con) . "');</script>";
                }
            } else {
                echo "<script>alert('Failed to upload image.');</script>";
            }
        } else {
            echo "<script>alert('Invalid file type. Only images are allowed.');</script>";
        }
    }
    ?>

    <?php
    // Delete logic (if triggered by GET param)
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $del = mysqli_query($con, "DELETE FROM `categories` WHERE id=$id");
        if ($del) {
            echo "<script>alert('Data deleted'); location.assign('fetching.php');</script>";
        }
    }
    ?>

    <!-- Footer -->
    <?php include("footer.php"); ?>
</div>

<!-- Scroll to Top Button -->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ready to Leave?</h5>
                <button class="close" type="button" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">Select "Logout" below if you are ready to end your session.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <a class="btn btn-primary" href="login.php">Logout</a>
            </div>
        </div>
    </div>
</div>

<!-- Core Scripts -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>

<!-- Charts (if needed) -->
<script src="vendor/chart.js/Chart.min.js"></script>
<script src="js/demo/chart-area-demo.js"></script>
<script src="js/demo/chart-pie-demo.js"></script>

</body>
</html>
