<?php include("connection.php"); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>FAQs - Frequently Asked Questions</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Common CSS -->
    <link rel="icon" type="image/png" href="images/icons/zufelogo-removebg-preview.png"/>
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="fonts/iconic/css/material-design-iconic-font.min.css">
    <link rel="stylesheet" href="vendor/animsition/css/animsition.min.css">
    <link rel="stylesheet" href="vendor/select2/select2.min.css">
    <link rel="stylesheet" href="vendor/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="css/util.css">
    <link rel="stylesheet" href="css/main.css">

    <style>
        body {
            background-color: #f8f9fa;
        }
        .faq-section {
            background: #fff;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
        }
        .faq-section h2 {
            font-weight: bold;
        }
        .faq-section p {
            font-size: 16px;
            line-height: 1.7;
            color: #555;
        }
        .text-muted{
            font-size: 18px !important;
        }
        .faq-question {
            margin-top: 25px;
            font-weight: bold;
            color: black;
            font-size: 17px;
            margin-bottom: 8px;
            cursor: default; /* No pointer */
        }
        .faq-answer {
            color: #555;
            font-size: 18px;
            margin-bottom: 15px;
        }
    </style>
</head>

<body class="animsition">

<?php include("header.php"); ?>

<section class="bg0 p-t-80 p-b-80 mt-4">
    <div class="container mt-4">
        <div class="row align-items-stretch mt-4">
            <!-- Left: FAQ Content -->
            <div class="col-lg-6 col-md-12 mb-4">
                <div class="faq-section">
                    <h4 class="ltext-105 text-dark txt-center mb-4">Frequently Asked Questions</h4>
                    <p class="text-center text-muted mb-5">Got a question? Start here — we’ve answered the most common ones for your ease.</p>

                    <div class="faq-list">
                        <div class="faq-item">
                            <div class="faq-question">Q: How long does shipping take?</div>
                            <div class="faq-answer">A: Shipping usually takes 3–7 business days, depending on your location.</div>
                        </div>
                        <div class="faq-item">
                            <div class="faq-question">Q: Can I return an item?</div>
                            <div class="faq-answer">A: Yes, you can return or exchange any item within 14 days of delivery.</div>
                        </div>
                        <div class="faq-item">
                            <div class="faq-question">Q: Do you ship internationally?</div>
                            <div class="faq-answer">A: Absolutely. We offer worldwide shipping with trusted courier partners.</div>
                        </div>
                        <div class="faq-item">
                            <div class="faq-question">Q: What if my item is damaged or doesn’t fit?</div>
                            <div class="faq-answer">A: Just reach out to our support team — we’ll help you with a replacement or return.</div>
                        </div>
                        <div class="faq-item">
                            <div class="faq-question">Q: How can I track my order?</div>
                            <div class="faq-answer">A: Use your order ID or tracking number on our <a href="track_order.php" style="color: #321961;">Track Order</a> page to see real-time updates.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Image Card -->
            <div class="col-lg-6 col-md-12 mb-4">
                <div class="card shadow-sm h-100 border-0" style="border-radius: 15px;">
            <div class="card-body p-0">
    <img src="images/banfaqs.png" alt="FAQs Visual" class="img-fluid w-100" style="height: 100%; object-fit: cover; border-radius: 15px;">
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
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
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
