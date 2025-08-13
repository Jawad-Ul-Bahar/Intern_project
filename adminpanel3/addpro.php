<?php include("connection.php") ?> <!-- Database connection -->

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta and Title -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>SB Admin 2 - Dashboard</title>

    <!-- Custom Fonts and CSS -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">

<!-- Wrapper -->
<div id="wrapper">

    <!-- Sidebar -->
    <?php include("aside.php"); ?>

    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="row">
            <h2>Add Product</h2>
        </div>

        <!-- Product Add Form -->
        <div class="container">
            <form action="" method="post" enctype='multipart/form-data'>

                <!-- Product Name -->
                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="3" required></textarea>
                </div>

                <!-- Price -->
                <div class="form-group">
                    <label>Price</label>
                    <input type="text" name="price" class="form-control" required>
                </div>

                <!-- Quantity -->
                <div class="form-group">
                    <label>Quantity</label>
                    <input type="text" name="qty" class="form-control" required>
                </div>

                <!-- Color -->
                <div class="form-group">
                    <label>Colors (comma-separated)</label>
                    <input type="text" name="colors" class="form-control" placeholder="Red, Blue, Green">
                </div>

                <!-- Shirt Type -->
                <div class="form-group">
                    <label>Shirt Type</label>
                    <input type="text" name="shirt_type" class="form-control" placeholder="e.g., Oversized Tee, Structured Polo">
                </div>

                <!-- Size -->
                <div class="form-group">
                    <label>Sizes (comma-separated)</label>
                    <input type="text" name="sizes" class="form-control" placeholder="S, M, L, XL">
                </div>

                <!-- Category -->
                <div class="form-group">
                    <label>Category</label>
                    <select name="cat" class="form-control">
                        <option value="">Select Category</option>
                        <?php 
                        $q = mysqli_query($con, "SELECT * FROM categories");
                        while($cat = mysqli_fetch_array($q)){
                            echo "<option value='{$cat[0]}'>{$cat[1]}</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Tax Percentage -->
                <div class="form-group">
                    <label>Tax Percentage (%)</label>
                    <input type="number" name="tax_percent" class="form-control" step="0.01" placeholder="e.g., 5 or 12.5">
                </div>

                <!-- Show Tax -->
                <div class="form-group">
                    <label>Show Tax Separately?</label>
                    <select name="show_tax" class="form-control">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>

                <!-- Product Images -->
                <div class="form-group">
                    <label>Product Images (color-wise)</label>
                    <p class="text-muted"><small><b>Note:</b> For each color variant, upload two images — one front view and one back view <b>(for hover effect)</b>. Set the image that should appear on hover as <b>Back</b> in the dropdown.</small></p>
                    <div id="img-group">
                        <div class="input-group mb-2">
                            <input type="file" name="img[]" class="form-control" required>
                            <input type="text" name="img_color[]" class="form-control" placeholder="Color name: Use exact names given above.">
                            <select name="img_type[]" class="form-control" required>
                                <option value="front">Front</option>
                                <option value="back">Back</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" id="addMoreBtn" class="btn btn-sm btn-secondary">+ Add More</button>
                </div>

                <!-- Submit Button -->
                <input type="submit" value="Add Product" class="btn btn-info" name="btn_pro">

            </form>

            <?php
            if (isset($_POST['btn_pro'])) {
                $pname = $_POST['name'];
                $desc = $_POST['description'];
                $price = $_POST['price'];
                $qty = $_POST['qty'];
                $color = $_POST['colors'];
                $shirt_type = $_POST['shirt_type'];
                $size = $_POST['sizes'];
                $cat = $_POST['cat'];
                $tax_percent = $_POST['tax_percent'] ?? 0;
                $show_tax = $_POST['show_tax'] ?? 0;

                // Find the first front image to use as main product image
                $mainImage = 'default.png';

                // Pre-check for first front image
                if (isset($_FILES['img']['tmp_name'])) {
                    foreach ($_FILES['img']['tmp_name'] as $key => $tmp_name) {
                        if (!empty($tmp_name) && $_POST['img_type'][$key] === 'front') {
                            $file_name = $_FILES['img']['name'][$key];
                            $ext = pathinfo($file_name, PATHINFO_EXTENSION);
                            if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'webp'])) {
                                $mainImage = time() . '_main_' . rand(1000, 9999) . '.' . $ext;
                                move_uploaded_file($tmp_name, "img/" . $mainImage);
                                break; // Use first front image as main
                            }
                        }
                    }
                }

                // ✅ INSERT PRODUCT FIRST with actual main image
                $q = mysqli_query($con, "INSERT INTO `products`(`name`, `description`, `price`, `qty`, `colors`, `shirt_type`, `sizes`, `cat_id`, `image`, `tax_percent`, `show_tax`) 
                VALUES ('$pname','$desc','$price','$qty','$color','$shirt_type','$size','$cat','$mainImage','$tax_percent','$show_tax')");

                if ($q) {
                    $product_id = mysqli_insert_id($con);
                    $main_image_used = false;

                    // Process all images
                    foreach ($_FILES['img']['tmp_name'] as $key => $tmp_name) {
                        if (!empty($tmp_name)) {
                            $file_name = $_FILES['img']['name'][$key];
                            $img_color = trim($_POST['img_color'][$key]);
                            $img_type = $_POST['img_type'][$key]; // front/back

                            $ext = pathinfo($file_name, PATHINFO_EXTENSION);
                            if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'webp'])) {
                                
                                // If this is the first front image and we used it as main, reuse it
                                if ($img_type === 'front' && !$main_image_used && $mainImage !== 'default.png') {
                                    $new_name = $mainImage; // Use the already uploaded main image
                                    $main_image_used = true;
                                } else {
                                    // Upload new image
                                    $new_name = time() . '_' . rand(1000, 9999) . '.' . $ext;
                                    move_uploaded_file($tmp_name, "img/" . $new_name);
                                }

                                // ✅ Insert into product_images table (color can be empty)
                                mysqli_query($con, "INSERT INTO `product_images`(`product_id`, `color`, `image`, `type`) 
                                VALUES ('$product_id', '$img_color', '$new_name', '$img_type')");
                            }
                        }
                    }

                    echo "<script>
                            alert('Product added successfully with all images!');
                            window.location.href = 'addpro.php';
                        </script>";
                } else {
                    echo "<script>alert('Product insert failed');</script>";
                }
            }
            ?>

<script>
// 1. Add input group on button click
document.getElementById('addMoreBtn').addEventListener('click', function () {
    const imgGroup = document.getElementById('img-group');

    const inputGroup = document.createElement('div');
    inputGroup.className = 'input-group mb-2';

    inputGroup.innerHTML = `
        <div class="input-group-prepend">
            <button class="btn btn-danger remove-btn" type="button">❌</button>
        </div>
        <input type="file" name="img[]" class="form-control" required>
        <input type="text" name="img_color[]" class="form-control" placeholder="Color name (can be empty for back)">
        <select name="img_type[]" class="form-control" required>
            <option value="front">Front</option>
            <option value="back">Back</option>
        </select>
    `;

    imgGroup.appendChild(inputGroup);
});

// 2. Remove button functionality using event delegation
document.getElementById('img-group').addEventListener('click', function(e) {
    if (e.target && e.target.classList.contains('remove-btn')) {
        const group = e.target.closest('.input-group');
        if (group) group.remove();
    }
});
</script>

</script>
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>
</body>
</html>
