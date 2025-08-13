<?php
// Include database connection
include("connection.php");

// Add new delivery area and charges
if (isset($_POST['add'])) {
    $area = $_POST['area_name'];
    $charges = $_POST['charges'];
    mysqli_query($con, "INSERT INTO delivery_settings (area_name, charges) VALUES ('$area', '$charges')");
}

// Update existing delivery charges
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $charges = $_POST['charges'];
    mysqli_query($con, "UPDATE delivery_settings SET charges = '$charges' WHERE id = '$id'");
}

// Delete a delivery area entry
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($con, "DELETE FROM delivery_settings WHERE id = '$id'");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Manage Delivery Charges</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Include CSS files for styling -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body id="page-top">

<div id="wrapper">
    <?php include("aside.php"); // Sidebar include ?>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <!-- Main Page Content -->
            <div class="container-fluid mt-4">

                <!-- Page Heading -->
                <h1 class="h3 mb-4 text-gray-800">Manage Delivery Charges</h1>

                <!-- Add New Area Form -->
                <form method="POST" class="mb-4">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <input type="text" name="area_name" class="form-control" placeholder="Area Name" required>
                        </div>
                        <div class="col-md-4">
                            <input type="number" name="charges" class="form-control" placeholder="Charges" required>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" name="add" class="btn btn-primary w-100">Add Area</button>
                        </div>
                    </div>
                </form>

                <!-- Display Existing Areas -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-primary">
                            <tr>
                                <th>ID</th>
                                <th>Area</th>
                                <th>Charges</th>
                                <th>Update</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        // Fetch and display all delivery area records
                        $res = mysqli_query($con, "SELECT * FROM delivery_settings");
                        while ($row = mysqli_fetch_assoc($res)) {
                            echo "<tr>
                                <form method='POST'>
                                    <td>{$row['id']}</td>
                                    <td>{$row['area_name']}</td>
                                    <td>
                                        <input type='number' name='charges' value='{$row['charges']}' class='form-control' required>
                                        <input type='hidden' name='id' value='{$row['id']}'>
                                    </td>
                                    <td><button name='update' class='btn btn-success btn-sm'>Update</button></td>
                                    <td><a href='?delete={$row['id']}' class='btn btn-danger btn-sm' onclick=\"return confirm('Delete this area?')\">Delete</a></td>
                                </form>
                            </tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
        <?php include("footer.php"); // Footer include ?>
    </div>
</div>

<!-- Include JS scripts -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>
</body>
</html>
