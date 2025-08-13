<?php
include('connection.php');

/* ---- Delete product if ?del=id is present ---- */
if (isset($_GET['del'])) {
    $delid = intval($_GET['del']);
    mysqli_query($con, "DELETE FROM products WHERE id = $delid") or die(mysqli_error($con));
    header("Location: viewpro.php");
    exit;
}

/* ---- Fetch all products ---- */
$q = mysqli_query($con, "SELECT * FROM products") or die(mysqli_error($con));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>All Products</title>

    <!-- SB‑Admin‑2 assets -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">
<div id="wrapper">

    <!-- Sidebar -->
    <?php include 'aside.php'; ?>

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
        <!-- Main Content -->
        <div id="content">
            <div class="container-fluid">
                <h2 class="h3 mb-4 text-gray-800">Products</h2>

                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead class="table-primary">
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Color</th>
                            <th>Shirt Type</th>
                            <th>Size</th>
                            <th>Category</th>
                            <th>Tax %</th>
                            <th>Show Tax</th>
                            <th>Images</th>
                            <th style="width:160px;">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php while ($pro = mysqli_fetch_assoc($q)) { ?>
                           <tr>
    <td><?= htmlspecialchars($pro['name']) ?></td>
    <td><?= htmlspecialchars($pro['description']) ?></td>
    <td><?= htmlspecialchars($pro['price']) ?></td>
    <td><?= htmlspecialchars($pro['qty']) ?></td>
    <td><?= htmlspecialchars($pro['colors']) ?></td>
    <td><?= htmlspecialchars($pro['shirt_type']) ?></td>
    <td><?= htmlspecialchars($pro['sizes']) ?></td>
    <td><?= htmlspecialchars($pro['cat_id']) ?></td>
    <td><?= htmlspecialchars($pro['tax_percent']) ?>%</td>
    <td><?= $pro['show_tax'] == 1 ? 'Yes' : 'No' ?></td>
    <td>
        <?php 
        $pid = $pro['id'];
        $img_query = mysqli_query($con, "SELECT * FROM product_images WHERE product_id = $pid");
        $images_by_color = [];
while ($img = mysqli_fetch_assoc($img_query)) {
    $color = $img['color'];
    $type = $img['type']; // front/back
    if (!isset($images_by_color[$color])) {
        $images_by_color[$color] = ['front' => null, 'back' => null];
    }
    $images_by_color[$color][$type] = $img['image'];
}

// Now show each color with front/back labels
foreach ($images_by_color as $color => $types) {
    echo "<div style='margin-bottom:8px'>";
    echo "<strong style='text-transform:capitalize;'>$color</strong><br>";
    
    if ($types['front']) {
        echo "<img src='img/{$types['front']}' height='50' style='border:1px solid #ccc; margin-right:5px;' title='Front'>";
    }

    if ($types['back']) {
        echo "<img src='img/{$types['back']}' height='50' style='border:1px solid #ccc;' title='Back'>";
    }

    echo "</div>";
}

        ?>
    </td>
    <td>
        <a href="proupdate.php?pupdate=<?= $pro['id'] ?>" class="btn btn-info btn-sm">Update</a>
        <a href="viewpro.php?del=<?= $pro['id'] ?>" onclick="return confirm('Delete this product?');" class="btn btn-danger btn-sm">Delete</a>
    </td>
</tr>

                        <?php } ?>
                        </tbody>
                    </table>
                </div>

            </div><!-- /.container-fluid -->
        </div><!-- End of Main Content -->

        <!-- Footer -->
        <?php include 'footer.php'; ?>
    </div><!-- End of Content Wrapper -->
</div><!-- End of Page Wrapper -->

<!-- Scroll to Top -->
<a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>

<!-- SB‑Admin‑2 scripts -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>
</body>
</html>
