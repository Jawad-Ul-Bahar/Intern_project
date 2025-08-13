<?php
session_start();
include('connection.php');

// Show all errors for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Redirect back if search is empty
if (isset($_GET['search']) && empty(trim($_GET['search']))) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

// Fetch user's wishlist products
$wishlist_products = [];
if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $wishlist_q = mysqli_query($con, "SELECT product_id FROM wishlist WHERE user_id = $uid");
    while ($w = mysqli_fetch_assoc($wishlist_q)) {
        $wishlist_products[] = $w['product_id'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Product</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="images/icons/zufelogo-removebg-preview.png"/>
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="fonts/iconic/css/material-design-iconic-font.min.css">
    <link rel="stylesheet" type="text/css" href="css/util.css">
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .search-container {
            text-align: center;
            margin: 100px 0 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
        }
        .search-form {
            display: inline-flex;
            align-items: center;
            position: relative;
            max-width: 500px;
            width: 100%;
        }
        .search-input {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ccc;
            border-radius: 50px;
            font-size: 16px;
            outline: none;
            transition: border-color 0.3s;
        }
        .search-input:focus {
            border-color: #007bff;
        }
        .search-button {
            position: absolute;
            right: 5px;
            padding: 10px 15px;
            background-color: #007bff;
            border: none;
            border-radius: 50%;
            color: white;
            font-size: 18px;
            cursor: pointer;
        }
        .search-button:hover {
            background-color: #0056b3;
        }
        .back-button {
            padding: 10px 15px;
            background-color: #f0f0f0;
            border-radius: 50px;
            color: #007bff;
            font-size: 16px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        .back-button:hover {
            background-color: #e0e0e0;
        }
        .color-circle {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid transparent;
            transition: 0.2s ease;
        }
        .btn-addwish-b2 .icon-heart1 {
            filter: grayscale(100%);
            transition: filter 0.3s ease;
        }
        .btn-addwish-b2:hover .icon-heart1,
        .btn-addwish-b2.active-wishlist .icon-heart1 {
            filter: sepia(1) saturate(700%) hue-rotate(265deg) brightness(0.4);
        }
        .btn-addwish-b2 {
            cursor: pointer;
        }
        .rating-stars {
            display: flex;
            gap: 5px;
            margin-top: 5px;
            cursor: pointer;
            font-size: 13px;
            color: #ccc;
        }
        .rating-stars .fa-star {
            color: #c2711aff;
        }
        .image-container {
            position: relative;
            width: 100%;
            height: auto;
        }
        .product-image {
            width: 100%;
            height: auto;
            display: block;
            transition: opacity 0.3s ease;
        }
        .back-img {
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0;
        }
        .image-container:hover .main-img {
            opacity: 0;
        }
        .image-container:hover .back-img {
            opacity: 1;
        }
        .color-btn.active {
            border: 2px solid #333 !important;
            transform: scale(1.1);
        }
    </style>
</head>
<body class="animsition">

    <?php include('header.php'); ?>

    <div class="search-container">
        <a style="color: #310E68 !important;" href="javascript:history.back()" class="back-button">
            <i style="color: #310E68 !important;" class="fa fa-arrow-left"></i> Back
        </a>
        <form action="product.php" method="GET" class="search-form">
            <input type="text" name="search" class="search-input" placeholder="Search for products..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" required>
            <button style="background-color: #310E68 !important;" type="submit" class="search-button"><i class="fa fa-search"></i></button>
        </form>
    </div>

    <?php
    $cat_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($cat_id > 0) {
        $cat_query = mysqli_query($con, "SELECT cat_name FROM categories WHERE id = $cat_id");
        if ($cat_query && mysqli_num_rows($cat_query) > 0) {
            $cat_row = mysqli_fetch_assoc($cat_query);
            echo "<h1 class='text-center ltext-103 cl5 mb-4'>" . htmlspecialchars($cat_row['cat_name']) . "</h1>";
        } else {
            echo "<h2 class='text-center text-danger mb-4'>Invalid Category</h2>";
        }
    }
    ?>

    <div class="bg0 m-t-23 p-b-140">
        <div class="container">
            <div class="row isotope-grid">
                <?php
                $search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
                $cat_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

                if ($search_query !== '') {
                    $pro = mysqli_query($con, "SELECT * FROM `products` WHERE name LIKE '%$search_query%'");
                } elseif ($cat_id > 0) {
                    $pro = mysqli_query($con, "SELECT * FROM `products` WHERE cat_id = $cat_id");
                } else {
                    $pro = mysqli_query($con, "SELECT * FROM `products`");
                }

                while ($row = mysqli_fetch_array($pro)) {
                    $sizes = explode(',', $row['sizes']);
                    $colors = explode(',', $row['colors']);

                    // Fetch color images
                    $product_id = $row['id'];
                    $color_imgs = mysqli_query($con, "SELECT * FROM product_images WHERE product_id = $product_id");
                    $color_image_map = [];
                    while ($imgrow = mysqli_fetch_assoc($color_imgs)) {
                        $color = strtolower(trim($imgrow['color']));
                        $type = strtolower(trim($imgrow['type']));
                        if (!isset($color_image_map[$color])) {
                            $color_image_map[$color] = [];
                        }
                        $color_image_map[$color][$type] = $imgrow['image'];
                    }
                ?>
                <div class="col-sm-6 col-md-4 col-lg-3 p-b-35 isotope-item women">
                    <div class="block2">
                        <div class="block2-pic hov-img0">
                            <?php
                            // Determine initial front and back image
                            $defaultFront = $row['image'];
                            $defaultBack = $row['image'];
                            if (!empty($colors)) {
                                $first_color = strtolower(trim($colors[0]));
                                if (isset($color_image_map[$first_color]['front'])) {
                                    $defaultFront = $color_image_map[$first_color]['front'];
                                }
                                if (isset($color_image_map['']['back'])) {
                                    $defaultBack = $color_image_map['']['back'];
                                } elseif (isset($color_image_map[$first_color]['back'])) {
                                    $defaultBack = $color_image_map[$first_color]['back'];
                                } else {
                                    $defaultBack = $defaultFront;
                                }
                            }
                            ?>
                            <a href="product-detail.php?proid=<?php echo $row['id']; ?>">
                                <div class="image-container">
                                    <img src="../adminpanel3/img/<?php echo $defaultFront; ?>" alt="Front Image" class="product-image main-img current-front">
                                    <img src="../adminpanel3/img/<?php echo $defaultBack; ?>" alt="Back Image" class="product-image back-img current-back">
                                </div>
                            </a>
                            <button class="block2-btn show-modal-btn block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04" data-id="<?php echo $row['id']; ?>">
                                Quick View
                            </button>
                        </div>
                        <div class="block2-txt flex-w flex-t p-t-14">
                            <div class="block2-txt-child1 flex-col-l">
                                <span class="stext-105 cl3" style="font-weight: bold;"><?php echo $row['name']; ?></span>
                                <span class="stext-105 cl3 mt-2"><?php echo $row['description']; ?></span>
                                <span class="stext-105 cl3 mt-2" style="font-weight: bold;">Rs: <?php echo $row['price']; ?></span>
                                <span class="stext-104 cl3 mt-2">
                                    <b>Sizes:
                                        <?php foreach ($sizes as $size) {
                                            echo "<span style='margin-right:5px;'>$size</span>";
                                        } ?>
                                    </b>
                                </span>
                                <div style="margin-top: 5px;">
                                    <?php foreach ($colors as $color):
                                        $color_trim = strtolower(trim($color));
                                        $front = $color_image_map[$color_trim]['front'] ?? $row['image'];
                                        $back  = $color_image_map[$color_trim]['back'] ?? $front;
                                    ?>
                                    <button class="color-btn color-circle"
                                        style="background: <?php echo $color ?>; border: 1px solid #ccc; width: 15px; height: 15px; margin: 2px; cursor:pointer;"
                                        title="<?php echo $color ?>"
                                        data-front-img="../adminpanel3/img/<?php echo htmlspecialchars($front) ?>"
                                        data-back-img="../adminpanel3/img/<?php echo htmlspecialchars($back) ?>">
                                    </button>
                                    <?php endforeach; ?>
                                </div>
                                <div class="rating-stars" data-product-id="<?php echo $row['id']; ?>" style="margin-top: 10px;">
                                    <i class="fa fa-star-o" data-value="1"></i>
                                    <i class="fa fa-star-o" data-value="2"></i>
                                    <i class="fa fa-star-o" data-value="3"></i>
                                    <i class="fa fa-star-o" data-value="4"></i>
                                    <i class="fa fa-star-o" data-value="5"></i>
                                </div>
                                <input type="hidden" id="rating-value-<?php echo $row['id']; ?>" value="">
                            </div>
                            <div class="block2-txt-child2 flex-r p-t-3">
                                <a href="#" class="btn-addwish-b2 js-addwish-b2" data-id="<?php echo $row['id']; ?>">
                                    <i class="fa fa-heart icon-heart1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
    
    <div id="productModal" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999;">
        <div class="modal-content" style="background: white; margin: 60px auto; padding: 20px; width: 90%; max-width: 400px; border-radius: 8px; position: relative;">
            <span class="close" style="position:absolute; top:10px; right:15px; font-size:25px; cursor:pointer; color:#888;">&times;</span>
            <div id="modal-body-content"></div>
        </div>
    </div>

    <?php include("footer.php"); ?>

    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="vendor/jquery/jquery-3.2.1.min.js"></script>
    <script src="vendor/animsition/js/animsition.min.js"></script>
    <script src="vendor/bootstrap/js/popper.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="vendor/select2/select2.min.js"></script>
    <script src="js/main.js"></script>

    <script>
        const wishlistItems = <?php echo json_encode($wishlist_products); ?>;

        document.addEventListener('DOMContentLoaded', () => {
            // Highlight already wishlisted products
            wishlistItems.forEach(id => {
                const btn = document.querySelector(`.btn-addwish-b2[data-id="${id}"]`);
                if (btn) {
                    btn.classList.add('active-wishlist');
                    const heartIcon = btn.querySelector('.icon-heart1');
                    if (heartIcon) {
                        heartIcon.src = 'images/icons/icon-heart-02.png';
                    }
                }
            });

            // Add to wishlist
            document.querySelectorAll('.btn-addwish-b2').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const id = this.dataset.id;
                    const button = this;
                    fetch('wishlist.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'product_id=' + encodeURIComponent(id)
                    })
                    .then(res => res.text())
                    .then(response => {
                        if (response === 'added' || response === 'exists') {
                            button.classList.add('active-wishlist');
                            const heartIcon = button.querySelector('.icon-heart1');
                            if (heartIcon) {
                                heartIcon.src = 'images/icons/icon-heart-02.png';
                            }
                            const headerWishlist = document.querySelector('.icon-header-noti[href="wishlist-view.php"]');
                            if (headerWishlist) {
                                let currentCount = parseInt(headerWishlist.getAttribute('data-notify')) || 0;
                                if (response === 'added') {
                                    currentCount++;
                                    headerWishlist.setAttribute('data-notify', currentCount);
                                }
                            }
                            swal(response === 'added' ? 'Added to wishlist!' : 'Already in wishlist!');
                        }
                    });
                });
            });

            // Quick View Modal logic
            document.querySelectorAll('.show-modal-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const id = this.dataset.id;
                    fetch('get-product-details.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'id=' + encodeURIComponent(id)
                    })
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById('modal-body-content').innerHTML = data;
                        document.getElementById('productModal').style.display = 'block';
                        // Re-initialize rating inside modal
                        initRatingStars();
                    });
                });
            });

            // Close modal
            const closeModalBtn = document.querySelector('#productModal .close');
            if (closeModalBtn) {
                closeModalBtn.addEventListener('click', () => {
                    document.getElementById('productModal').style.display = 'none';
                });
            }

            // Click outside to close modal
            window.addEventListener('click', function (e) {
                const modal = document.getElementById('productModal');
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });

            // Color button image switch (with hover back image)
            document.querySelectorAll('.block2').forEach(productBox => {
                const frontImg = productBox.querySelector('.current-front');
                const backImg = productBox.querySelector('.current-back');
                const colorButtons = productBox.querySelectorAll('.color-btn');
                colorButtons.forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.preventDefault();
                        // Remove active class
                        colorButtons.forEach(cb => cb.classList.remove('active'));
                        btn.classList.add('active');
                        const newFrontImg = btn.getAttribute('data-front-img');
                        const newBackImg = btn.getAttribute('data-back-img');
                        if (frontImg && newFrontImg) {
                            frontImg.src = newFrontImg;
                        }
                        if (backImg && newBackImg) {
                            backImg.src = newBackImg;
                        }
                    });
                });
            });

            // Initialize rating on load
            initRatingStars();

            // Function: Rating system
            function initRatingStars() {
                document.querySelectorAll('.rating-stars').forEach(container => {
                    const productId = container.getAttribute('data-product-id');
                    const stars = container.querySelectorAll('i');
                    const hiddenInput = document.getElementById('rating-value-' + productId);
                    stars.forEach(star => {
                        star.addEventListener('click', function () {
                            const rating = parseInt(this.getAttribute('data-value'));
                            // Update UI
                            stars.forEach(s => {
                                s.classList.remove('fa-star', 'fa-star-o');
                                s.classList.add(parseInt(s.getAttribute('data-value')) <= rating ? 'fa-star' : 'fa-star-o');
                            });
                            if (hiddenInput) {
                                hiddenInput.value = rating;
                            }
                        });
                    });
                });
            }
        });
    </script>
</body>
</html>