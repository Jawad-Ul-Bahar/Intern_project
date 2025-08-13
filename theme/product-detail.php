<?php
include('query.php');

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Product Detail</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Stylesheets -->
      <link rel="icon" type="image/png" href="images/icons/zufelogo-removebg-preview.png"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="fonts/iconic/css/material-design-iconic-font.min.css">
    <link rel="stylesheet" type="text/css" href="fonts/linearicons-v1.0.0/icon-font.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
    <link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/animsition/css/animsition.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/daterangepicker/daterangepicker.css">
    <link rel="stylesheet" type="text/css" href="vendor/slick/slick.css">
    <link rel="stylesheet" type="text/css" href="vendor/MagnificPopup/magnific-popup.css">
    <link rel="stylesheet" type="text/css" href="vendor/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" type="text/css" href="css/util.css">
    <link rel="stylesheet" type="text/css" href="css/main.css">

    <!-- Styles for tick mark effect -->
    <style>
    /* Common label style */
    .option-label {
        display: inline-block;
        margin: 5px;
        position: relative;
        cursor: pointer;
    }

    /* Hide default radio button */
    .option-label input {
        display: none;
    }

    /* SIZE as circle swatches with text inside */
    .size-circle {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        border: 2px solid #ccc;
        background: white;
        color: #310E68;
        font-weight: bold;
        font-size: 14px;
        text-align: center;
        line-height: 23px;
        transition: 0.2s ease;
    }
    .option-label input:checked + .size-circle {
        background-color: #310E68;
        color: white;
        border: 2px solid #310E68;
    }

    /* COLOR swatches */
    .color-circle {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 2px solid transparent;
        transition: 0.2s ease;
    }
    .option-label input:checked + .color-circle {
        border: 2px solid #310E68;
        box-shadow: 0 0 0 2px #310E6888;
    }

    /* Tick mark on selection (optional) */
    .option-label .tick {
        position: absolute;
        top: -6px;
        right: -6px;
        background: white;
        color: #310E68;
        border-radius: 50%;
        font-size: 12px;
        padding: 1px 4px;
        display: none;
    }
    .option-label input:checked ~ .tick {
        display: block;
    }
    </style>
</head>

<body class="animsition">

    <!-- Header Include -->
    <?php include('header.php'); ?>

    <br><br><br>
    <form method="POST" action="add_to_cart.php">
        <!-- Product Detail Section -->
        <section class="sec-product-detail bg0 p-t-65 p-b-60">
            <div class="container">
                <div class="row">
                    <?php
                    if (isset($_GET['proid'])) {
                        $proid = $_GET['proid'];
                        $q = mysqli_query($con, "SELECT * FROM `products` WHERE id='$proid'");
                        $pro = mysqli_fetch_array($q);

                        // Get color images from product_images table
                        $color_imgs = mysqli_query($con, "SELECT * FROM product_images WHERE product_id = $proid");
                        $color_image_map = [];
                        while ($imgrow = mysqli_fetch_assoc($color_imgs)) {
                            $color = strtolower(trim($imgrow['color']));
                            $type = strtolower(trim($imgrow['type'])); // 'front' or 'back'

                            if (!isset($color_image_map[$color])) {
                                $color_image_map[$color] = [];
                            }
                            $color_image_map[$color][$type] = $imgrow['image'];
                        }

                        // Get colors array
                        $colors = explode(',', $pro[5]);
                    ?>

                    <!-- Product Images -->
                    <div class="col-md-6 col-lg-7 p-b-30">
                        <div class="p-l-25 p-r-30 p-lr-0-lg">
                            <div class="wrap-slick3 flex-sb flex-w">
                                <div class="wrap-slick3-dots"></div>
                                <div class="wrap-slick3-arrows flex-sb-m flex-w"></div>

                                <div class="slick3 gallery-lb">
                                    <div class="item-slick3" data-thumb="../adminpanel3/img/<?php echo $pro[9] ?>">
                                        <div>
                                            <img id="main-product-image" style="height: 500px; width: auto" src="../adminpanel3/img/<?php echo $pro[9] ?>" alt="IMG-PRODUCT">
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="col-md-6 col-lg-5 p-b-30">
                        <div class="p-r-50 p-t-5 p-lr-0-lg">

                            <small>
                                <p class="text-left mb-2"><strong><b>Prod Code:</b> &nbsp;</strong>F1296/106/122-50615863</p>
                            </small>
                            <h4 class="mtext-105 cl2 js-name-detail p-b-14 mt-2">
                                <?php echo $pro[1] ?>
                            </h4>
                            <p class="text-left"><b>Price: </b> <?php echo $pro[3] ?></p>
                            <div class="mt-4 mb-4">
                                <p><b>Description: </b></p>
                                <p class="text-muted" style="font-size: 15px; line-height: 1.7;">
                                    <?php echo $pro[2]; ?>
                                </p>
                            </div>

                            <p class=" mt-2"><b>Type: </b>  <?php echo $pro[6]; ?></p>
       
                            
                            <!-- SIZE Swatches (Styled like colors with text) -->
                            <div class="text-left mt-2 d-flex align-items-center flex-wrap">
                                <b class="mr-2" style="white-space: nowrap;">Sizes: </b>
                                <?php
                                $sizes = explode(",", $pro[7]);
                                foreach ($sizes as $size) {
                                    $size = trim($size);
                                    echo "
                                    <label class='option-label' title='Size $size'>
                                        <input type='radio' name='selected_size' value='$size' required>
                                        <div class='size-circle'>$size</div>
                                        <div class='tick'>✔</div>
                                    </label>";
                                }
                                ?>
                            </div>

                            <!-- COLOR Swatches -->
                            <div class="text-left mt-2 d-flex align-items-center flex-wrap">
                                <b class="mr-2" style="white-space: nowrap;">Colors: </b>
                                <?php
                                foreach ($colors as $color) {
                                    $color = trim($color);
                                    $color_lower = strtolower($color);
                                    
                                    // Get front image for this color (fallback to main image)
                                    $front_img = $color_image_map[$color_lower]['front'] ?? $pro[9];
                                    
                                    echo "
                                    <label class='option-label color-option' title='$color' 
                                           data-color='$color_lower'
                                           data-front-img='../adminpanel3/img/$front_img'>
                                        <input type='radio' name='selected_color' value='$color' required>
                                        <div class='color-circle' style='background-color: $color;'></div>
                                        <div class='tick'>✔</div>
                                    </label>";
                                }
                                ?>
                            </div>

                            <!-- Quantity & Add to Cart -->
                            <div class="p-t-33">
                                <div class="flex-w flex-l-m p-b-10">
                                    <div class="size-204 flex-w flex-m respon6-next">

                                        <div class="wrap-num-product flex-w m-r-20 m-tb-10">
                                            <div class="btn-num-product-down cl8 hov-btn3 trans-04 flex-c-m">
                                                <i class="fs-16 zmdi zmdi-minus"></i>
                                            </div>
                                            <input class="mtext-104 cl3 txt-center num-product" type="number" name="proqty" value="1">
                                            <div class="btn-num-product-up cl8 hov-btn3 trans-04 flex-c-m">
                                                <i class="fs-16 zmdi zmdi-plus"></i>
                                            </div>
                                        </div>
                                        

                                        <!-- Hidden Inputs -->
                                        <input type="hidden" name="proid" value="<?php echo $pro[0]; ?>">
                                        <input type="hidden" name="proname" value="<?php echo $pro[1]; ?>">
                                        <input type="hidden" name="proprice" value="<?php echo $pro[3]; ?>">
                                        <input type="hidden" id="selected-image" name="proimg" value="<?php echo $pro[9]; ?>">
                                        <input type="hidden" name="description" value="<?php echo $pro[2]; ?>">
                                        <input type="hidden" name="shirttype" value="<?php echo $pro[6]; ?>">

                                        <!-- Submit Button -->
                                       <button type="submit" name="addtocart" class="flex-c-m stext-101 cl0 size-101 bg1 bor1 hov-btn1 p-lr-15 trans-04 js-addcart-detail m-tb-10">
                                            Add to Cart
                                        </button>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </section>
    </form>

    <!-- Footer Include -->
    <?php include("footer.php"); ?>

    <!-- Back to Top Button -->
    <div class="btn-back-to-top" id="myBtn">
        <span class="symbol-btn-back-to-top">
            <i class="zmdi zmdi-chevron-up"></i>
        </span>
    </div>

    <!-- JS Scripts -->
    <script src="vendor/jquery/jquery-3.2.1.min.js"></script>
    <script src="vendor/animsition/js/animsition.min.js"></script>
    <script src="vendor/bootstrap/js/popper.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="vendor/select2/select2.min.js"></script>
    <script>
        $(".js-select2").each(function () {
            $(this).select2({
                minimumResultsForSearch: 20,
                dropdownParent: $(this).next('.dropDownSelect2')
            });
        });
    </script>
    <script src="vendor/daterangepicker/moment.min.js"></script>
    <script src="vendor/daterangepicker/daterangepicker.js"></script>
    <script src="vendor/slick/slick.min.js"></script>
    <script src="js/slick-custom.js"></script>
    <script src="vendor/parallax100/parallax100.js"></script>
    <script>
        $('.parallax100').parallax100();
    </script>
    <script src="vendor/MagnificPopup/jquery.magnific-popup.min.js"></script>
    <script>
        $('.gallery-lb').each(function () {
            $(this).magnificPopup({
                delegate: 'a',
                type: 'image',
                gallery: {
                    enabled: true
                },
                mainClass: 'mfp-fade'
            });
        });
    </script>
    <script src="vendor/isotope/isotope.pkgd.min.js"></script>
    <script src="vendor/sweetalert/sweetalert.min.js"></script>
    

    <!-- Custom JavaScript for Color Change -->
    <script>
  $(document).ready(function() {
    // Color selection functionality  
    $('.color-option').on('click', function() {
        const frontImg = $(this).data('front-img');
        
        // Update main product image
        $('#main-product-image').attr('src', frontImg);
        
        // Update all image references in slick slider
        $('.item-slick3').attr('data-thumb', frontImg);
        $('.item-slick3 img').attr('src', frontImg);
        
        // Update hidden input for cart (remove path prefix)
        $('#selected-image').val(frontImg.replace('../adminpanel3/img/', ''));
        
        // Update thumbnail dots if they exist
        $('.slick-dots li img, .wrap-slick3-dots img').each(function() {
            $(this).attr('src', frontImg);
        });
        
        // Force refresh all image elements that might be cached
        setTimeout(function() {
            $('img[src*="' + frontImg.split('/').pop() + '"]').each(function() {
                $(this).attr('src', frontImg);
            });
        }, 100);
    });
});
    </script>

    <script>
        $('.js-addwish-b2, .js-addwish-detail').on('click', function (e) {
            e.preventDefault();
        });

        $('.js-addwish-b2').each(function () {
            var nameProduct = $(this).parent().parent().find('.js-name-b2').html();
            $(this).on('click', function () {
                swal(nameProduct, "is added to wishlist !", "success");
                $(this).addClass('js-addedwish-b2');
                $(this).off('click');
            });
        });

        $('.js-addwish-detail').each(function () {
            var nameProduct = $(this).parent().parent().parent().find('.js-name-detail').html();
            $(this).on('click', function () {
                swal(nameProduct, "is added to wishlist !", "success");
                $(this).addClass('js-addedwish-detail');
                $(this).off('click');
            });
        });

        $('form').on('submit', function (e) {
            var nameProduct = $(this).find('.js-name-detail').html();
            swal({
                title: nameProduct,
                text: "has been added to cart!",
                icon: "success",
                buttons: false,
                timer: 1800
            });
        });

    </script>
    <script src="vendor/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script>
        $('.js-pscroll').each(function () {
            $(this).css('position', 'relative');
            $(this).css('overflow', 'hidden');
            var ps = new PerfectScrollbar(this, {
                wheelSpeed: 1,
                scrollingThreshold: 1000,
                wheelPropagation: false,
            });

            $(window).on('resize', function () {
                ps.update();
            });
        });
    </script>
    <script src="js/main.js"></script>
</body>

</html>