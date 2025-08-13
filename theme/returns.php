<?php include("connection.php"); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Returns & Exchanges</title>
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .returns-section {
            background: #fff;
            border-radius: 16px;
            padding: 45px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
            transition: 0.3s ease;
            height: 100%;
        }
        .returns-section h1 {
            font-weight: 700;
            color: #000000;
        }
        .returns-section p {
            font-size: 16.5px;
            line-height: 1.8;
            color: #444;
        }
        .text-muted {
            font-size: 22px !important;
        }
        .returns-btn {
            background-color: #321961;
            color: white;
            border: none;
            border-radius: 50px;
            padding: 14px 35px;
            font-size: 17px;
            font-weight: 500;
            box-shadow: 0 6px 18px rgba(50, 25, 97, 0.3);
            transition: all 0.3s ease;
            text-decoration: none;
        }
        .returns-btn:hover {
            background-color: #22124e;
            color: white;
            transform: translateY(-2px);
        }
        .icon-bullet {
            color: #321961;
            margin-right: 8px;
        }
    </style>
</head>

<body class="animsition">

<?php include("header.php"); ?>

<!-- Returns Section -->
<section class="bg0 p-t-80 p-b-80 mt-4">
    <div class="container mt-4">
        <div class="row align-items-stretch mt-4">
            <!-- Left Column - Text -->
            <div class="col-lg-6 col-md-12 mb-4">
                <div class="returns-section">
                    <h4 class="ltext-105 text-dark txt-center mb-4">Returns & Exchanges</h4>
                    <p class="text-center text-muted mb-4">Not quite right? No problem. We're here to help.</p>

                    <p><i class="fa fa-check icon-bullet"></i> <strong>Return Period:</strong> You have <strong>14 days from the delivery date</strong> to return or exchange your item.</p><br>

                    <p><i class="fa fa-rocket icon-bullet"></i> <strong>Fast & Hassle-Free:</strong> Whether it’s the wrong size or just a change of mind, we’ve made returns <strong>easy, smooth, and stress-free</strong>.</p><br>

                    <p><i class="fa fa-cube icon-bullet"></i> <strong>Item Condition:</strong> Please make sure the product is in its <strong>original condition and packaging</strong> when returning.</p><br>

                    <p><i class="fa fa-refresh icon-bullet"></i> <strong>Processing:</strong> Once we receive the item, refunds or exchanges are processed <strong>promptly</strong>.</p><br>

                    <p><i class="fa fa-headphones icon-bullet"></i> <strong>Need Help?</strong> Our team is always ready to assist. Just click below to get in touch.</p>

                    <div class="text-center mt-5">
                        <a href="contact.php" class="returns-btn">📞 Contact Support</a>
                    </div>
                </div>
            </div>

            <!-- Right Column - Image -->
            <div class="col-lg-6 col-md-12 mb-4">
                <div class="card shadow-sm h-100 border-0" style="border-radius: 15px; overflow: hidden;">
                    <div class="card-body p-0">
                        <img src="images/banreturn.png" alt="Returns Banner" class="img-fluid w-100" style="height: 100%; object-fit: cover; border-radius: 15px;">
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
