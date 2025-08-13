<?php include("connection.php") ?> <!-- Database connection -->

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta and Title -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>SB Admin 2 - Update Product</title>

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
            <h2>Update Product</h2>
        </div>

        <?php
        // Get product ID
        if (isset($_GET['pupdate'])) {
            $up = intval($_GET['pupdate']);
        } elseif (isset($_POST['id'])) {
            $up = intval($_POST['id']);
        } else {
            echo "<div class='alert alert-danger'>Product ID is missing.</div>";
            exit;
        }

        // Get product info
        $query = mysqli_query($con, "SELECT * FROM products WHERE id=$up");
        if (!$query || mysqli_num_rows($query) === 0) {
            echo "<div class='alert alert-danger'>Product not found.</div>";
            exit;
        }
        $col = mysqli_fetch_assoc($query);
        ?>

        <!-- Product Update Form -->
        <div class="container">
            <form action="" method="post" enctype='multipart/form-data'>
                <input type="hidden" name="id" value="<?= $up ?>">

                <!-- Product Name -->
                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($col['name']) ?>" class="form-control" required>
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="3" required><?= htmlspecialchars($col['description']) ?></textarea>
                </div>

                <!-- Price -->
                <div class="form-group">
                    <label>Price</label>
                    <input type="text" name="price" value="<?= htmlspecialchars($col['price']) ?>" class="form-control" required>
                </div>

                <!-- Quantity -->
                <div class="form-group">
                    <label>Quantity</label>
                    <input type="text" name="qty" value="<?= htmlspecialchars($col['qty']) ?>" class="form-control" required>
                </div>

                <!-- Color -->
                <div class="form-group">
                    <label>Colors (comma-separated)</label>
                    <input type="text" name="colors" value="<?= htmlspecialchars($col['colors']) ?>" class="form-control" placeholder="Red, Blue, Green">
                </div>

                <!-- Shirt Type -->
                <div class="form-group">
                    <label>Shirt Type</label>
                    <input type="text" name="shirt_type" value="<?= htmlspecialchars($col['shirt_type']) ?>" class="form-control" placeholder="e.g., Oversized Tee, Structured Polo">
                </div>

                <!-- Size -->
                <div class="form-group">
                    <label>Sizes (comma-separated)</label>
                    <input type="text" name="sizes" value="<?= htmlspecialchars($col['sizes']) ?>" class="form-control" placeholder="S, M, L, XL">
                </div>

                <!-- Category -->
                <div class="form-group">
                    <label>Category</label>
                    <select name="cat" class="form-control">
                        <option value="">Select Category</option>
                        <?php 
                        $q = mysqli_query($con, "SELECT * FROM categories");
                        while($cat = mysqli_fetch_array($q)){
                            $selected = ($cat[0] == $col['cat_id']) ? 'selected' : '';
                            echo "<option value='{$cat[0]}' $selected>{$cat[1]}</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Tax Percentage -->
                <div class="form-group">
                    <label>Tax Percentage (%)</label>
                    <input type="number" name="tax_percent" value="<?= htmlspecialchars($col['tax_percent']) ?>" class="form-control" step="0.01" placeholder="e.g., 5 or 12.5">
                </div>

                <!-- Show Tax -->
                <div class="form-group">
                    <label>Show Tax Separately?</label>
                    <select name="show_tax" class="form-control">
                        <option value="0" <?= ($col['show_tax'] == 0) ? 'selected' : '' ?>>No</option>
                        <option value="1" <?= ($col['show_tax'] == 1) ? 'selected' : '' ?>>Yes</option>
                    </select>
                </div>

                <!-- Main Product Image -->
                <div class="form-group">
                    <label>Main Product Image (optional)</label>
                    <input type="file" name="main_image" class="form-control">
                    <?php if(!empty($col['image'])): ?>
                        <div class="mt-2">
                            <img src="img/<?= htmlspecialchars($col['image']) ?>" width="120" class="img-thumbnail">
                            <small class="d-block">Current main image</small>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Existing Images -->
                <div class="form-group">
                    <label>Existing Color-wise Images</label>
                    <div class="row">
                        <?php
                        $imgs = mysqli_query($con, "SELECT * FROM product_images WHERE product_id=$up ORDER BY type, color");
                        if(mysqli_num_rows($imgs) > 0):
                            while ($img = mysqli_fetch_assoc($imgs)) {
                                $type_badge = ($img['type'] == 'front') ? 'badge-primary' : 'badge-secondary';
                                echo "
                                <div class='col-md-3 col-sm-4 col-6 mb-3 text-center'>
                                    <div class='card'>
                                        <img src='img/{$img['image']}' class='card-img-top' style='height: 150px; object-fit: cover;'>
                                        <div class='card-body p-2'>
                                            <small class='d-block'><strong>Color:</strong> " . (empty($img['color']) ? 'N/A' : htmlspecialchars($img['color'])) . "</small>
                                            <span class='badge $type_badge'>" . ucfirst($img['type']) . "</span><br>
                                            <a href='?pupdate=$up&delimg={$img['id']}' class='btn btn-danger btn-sm mt-2' 
                                               onclick='return confirm(\"Are you sure you want to delete this image?\")'>Delete</a>
                                        </div>
                                    </div>
                                </div>";
                            }
                        else:
                            echo "<div class='col-12'><p class='text-muted'>No additional images found.</p></div>";
                        endif;
                        ?>
                    </div>
                </div>

                <!-- Add New Images -->
                <div class="form-group">
                    <label>Add New Product Images (color-wise)</label>
                    <p class="text-muted"><small>Note: Upload images for each color. Color field can be left empty for back images.</small></p>
                    <div id="img-group">
                        <div class="input-group mb-2">
                            <input type="file" name="img[]" class="form-control">
                            <input type="text" name="img_color[]" class="form-control" placeholder="Color name (can be empty for back)">
                            <select name="img_type[]" class="form-control">
                                <option value="front">Front</option>
                                <option value="back">Back</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" id="addMoreBtn" class="btn btn-sm btn-secondary">+ Add More</button>
                </div>

                <!-- Submit Button -->
                <input type="submit" value="Update Product" class="btn btn-success" name="btn_update">
                <a href="viewpro.php" class="btn btn-primary">Back to Products</a>

            </form>

            <?php
            // Handle update
            if (isset($_POST['btn_update'])) {
                $pname = mysqli_real_escape_string($con, $_POST['name']);
                $desc = mysqli_real_escape_string($con, $_POST['description']);
                $price = $_POST['price'];
                $qty = $_POST['qty'];
                $color = mysqli_real_escape_string($con, $_POST['colors']);
                $shirt_type = mysqli_real_escape_string($con, $_POST['shirt_type']);
                $size = mysqli_real_escape_string($con, $_POST['sizes']);
                $cat = $_POST['cat'];
                $tax_percent = $_POST['tax_percent'] ?? 0;
                $show_tax = $_POST['show_tax'] ?? 0;

                // Basic update query
                $update_fields = "`name`='$pname', `description`='$desc', `price`='$price', `qty`='$qty', 
                                `colors`='$color', `shirt_type`='$shirt_type', `sizes`='$size', `cat_id`='$cat',
                                `tax_percent`='$tax_percent', `show_tax`='$show_tax'";

                // Handle main image update
                if (!empty($_FILES['main_image']['name'])) {
                    $imageName = $_FILES['main_image']['name'];
                    $ext = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
                    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                    if (in_array($ext, $allowed)) {
                        $newImageName = time() . '_main_' . rand(1000, 9999) . '.' . $ext;
                        if(move_uploaded_file($_FILES['main_image']['tmp_name'], "img/$newImageName")) {
                            $update_fields .= ", `image`='$newImageName'";
                        }
                    }
                }

                // Update product
                $update_query = "UPDATE products SET $update_fields WHERE id=$up";
                $result = mysqli_query($con, $update_query);

                if ($result) {
                    // Handle new images
                    if (isset($_FILES['img']['tmp_name'])) {
                        foreach ($_FILES['img']['tmp_name'] as $key => $tmp_name) {
                            if (!empty($tmp_name)) {
                                $file_name = $_FILES['img']['name'][$key];
                                $img_color = trim($_POST['img_color'][$key]);
                                $img_type = $_POST['img_type'][$key];

                                $ext = pathinfo($file_name, PATHINFO_EXTENSION);
                                if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'webp'])) {
                                    $new_name = time() . '_' . rand(1000, 9999) . '.' . $ext;
                                    if(move_uploaded_file($tmp_name, "img/" . $new_name)) {
                                        mysqli_query($con, "INSERT INTO `product_images`(`product_id`, `color`, `image`, `type`) 
                                        VALUES ('$up', '$img_color', '$new_name', '$img_type')");
                                    }
                                }
                            }
                        }
                    }

                    echo "<script>
                            alert('Product updated successfully!');
                            window.location.href = 'viewpro.php';
                          </script>";
                } else {
                    echo "<script>alert('Product update failed: " . mysqli_error($con) . "');</script>";
                }
            }

            // Delete specific image
            if (isset($_GET['delimg'])) {
                $img_id = intval($_GET['delimg']);
                $img_q = mysqli_query($con, "SELECT image FROM product_images WHERE id=$img_id");
                if($img_q && mysqli_num_rows($img_q) > 0) {
                    $img_r = mysqli_fetch_assoc($img_q);
                    if(file_exists("img/" . $img_r['image'])) {
                        unlink("img/" . $img_r['image']);
                    }
                    mysqli_query($con, "DELETE FROM product_images WHERE id=$img_id");
                    echo "<script>alert('Image deleted successfully.'); window.location='proupdate.php?pupdate=$up';</script>";
                } else {
                    echo "<script>alert('Image not found.'); window.location='proupdate.php?pupdate=$up';</script>";
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
                    <input type="file" name="img[]" class="form-control">
                    <input type="text" name="img_color[]" class="form-control" placeholder="Color name (can be empty for back)">
                    <select name="img_type[]" class="form-control">
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

        </div>
    </div>

</div>

<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>
</body>
</html>