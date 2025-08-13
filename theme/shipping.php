<?php include("connection.php"); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Shipping Information</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- All includes retained -->
    <link rel="icon" type="image/png" href="images/icons/zufelogo-removebg-preview.png"/>
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="fonts/iconic/css/material-design-iconic-font.min.css">
    <link rel="stylesheet" href="fonts/linearicons-v1.0.0/icon-font.min.css">
    <link rel="stylesheet" href="vendor/animate/animate.css">
    <link rel="stylesheet" href="vendor/css-hamburgers/hamburgers.min.css">
    <link rel="stylesheet" href="vendor/animsition/css/animsition.min.css">
    <link rel="stylesheet" href="vendor/select2/select2.min.css">
    <link rel="stylesheet" href="vendor/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="css/util.css">
    <link rel="stylesheet" href="css/main.css">

    <style>
        body {
            background-color: #f8f9fa;
        }
        .shipping-section {
            background: #fff;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
            height: 100%;
        }
        .shipping-section h1 {
            font-weight: bold;
        }
        .shipping-section p {
            font-size: 16px;
            line-height: 1.7;
            color: #555;
        }
        .text-muted {
            font-size: 20px !important;
        }
        .shipping-btn {
            background-color: #321961;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-size: 16px;
            transition: background 0.3s;
            text-decoration: none;
        }
        .shipping-btn:hover {
            background-color: #22124e;
            color: white;
        }
    </style>
</head>

<body class="animsition">

<?php include("header.php"); ?>

<!-- Shipping Section -->
<section class="bg0 p-t-80 p-b-80 mt-4">
    <div class="container mt-4">
        <div class="row align-items-stretch mt-4">
            <!-- Left: Text Content -->
            <div class="col-lg-6 col-md-12 mb-4">
                <div class="shipping-section">
                    <h4 class="ltext-105 text-dark txt-center mb-4">Shipping Information</h4>
                    <p class="text-center text-muted mb-5">Fast. Reliable. Global.</p>

                    <p>We proudly offer <strong>worldwide shipping</strong> with trusted logistics partners to ensure your orders arrive quickly and safely.</p><br>

                    <p>Once your order is placed, we start processing it within <strong>1–2 business days</strong>. This includes order verification, quality check, and packaging. You’ll receive an email confirmation when your order is shipped.</p><br>

                    <p>As soon as your parcel is on the move, we’ll send you a <strong>tracking link</strong> so you can follow your order every step of the way — from our warehouse to your doorstep.</p><br>

                    <p>Your satisfaction is our priority, and our goal is to deliver a seamless experience from checkout to delivery.</p>

                    <div class="text-center mt-5">
                        <a href="contact.php" class="shipping-btn">📦 Contact Support</a>
                    </div>
                </div>
            </div>

            <!-- Right: Image -->
            <div class="col-lg-6 col-md-12 mb-4">
                <div class="card shadow-sm h-100 border-0" style="border-radius: 15px; overflow: hidden;">
                    <div class="card-body p-0">
                        <img src="images/banshipping.png" alt="Shipping Visual" class="img-fluid w-100" style="height: 100%; object-fit: cover; border-radius: 15px;">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include("footer.php"); ?>

<!-- Scripts -->
<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
<script src="vendor/animsition/js/animsition.min.js"></script>
<script src="vendor/bootstrap/js/popper.js"></script>
<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="vendor/select2/select2.min.js"></script>
<script>
    $(".js-select2").each(function(){
        $(this).select2({
            minimumResultsForSearch: 20,
            dropdownParent: $(this).next('.dropDownSelect2')
        });
    });
</script>
<script src="vendor/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script>
    $('.js-pscroll').each(function(){
        $(this).css('position','relative');
        $(this).css('overflow','hidden');
        var ps = new PerfectScrollbar(this, {
            wheelSpeed: 1,
            scrollingThreshold: 1000,
            wheelPropagation: false,
        });
        $(window).on('resize', function(){
            ps.update();
        });
    });
</script>
<script src="js/main.js"></script>

</body>
</html>
