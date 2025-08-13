<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer.php';
require 'Exception.php';
require 'SMTP.php';

// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

$successMsg = '';

// Handle contact form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'], $_POST['msg'])) {
    $mail = new PHPMailer(true);

    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'jawadulbahar@gmail.com';
        $mail->Password   = 'delwgxougwyvdpzi';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Set sender and recipient
        $mail->setFrom('jawadulbahar@gmail.com', 'Website Visitor');
        $mail->addReplyTo($_POST['email']);
        $mail->addAddress('jawadulbahar@gmail.com');

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Website Contact Message';
        $mail->Body    = nl2br(htmlspecialchars($_POST['msg']));
        $mail->AltBody = strip_tags($_POST['msg']);

        $mail->send();
        $successMsg = "✅ Message Sent Successfully!";
    } catch (Exception $e) {
        $successMsg = "❌ Message could not be sent. Error: {$mail->ErrorInfo}";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>Contact</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
</head>

<body class="animsition">

<?php include('header.php'); ?>

<!-- Page Title Section -->
<section class="bg-img1 txt-center p-lr-15 p-tb-92" style="background-image: url('images/bancontact.png'); height: 500px;">
</section>    

<!-- Contact Form Section -->
<section class="bg0 p-t-104 p-b-116">
    <div class="container">
            <h2 class="ltext-105 text-dark txt-center">Contact Us</h2><br><br>

        <div class="flex-w flex-tr">

            <!-- Form Column -->
            <div class="size-210 bor10 p-lr-70 p-t-55 p-b-70 p-lr-15-lg w-full-md">
                <form method="POST" action="">
                    <h4 class="mtext-105 cl2 txt-center p-b-30">Send us a message</h4>

                    <div class="bor8 m-b-20 how-pos4-parent">
                        <input class="stext-111 cl2 plh3 size-116 p-l-62 p-r-30"
                            type="email" name="email" placeholder="Your Email Address" required>
                        <img class="how-pos4 pointer-none" src="images/icons/icon-email.png" alt="ICON">
                    </div>

                    <div class="bor8 m-b-30">
                        <textarea class="stext-111 cl2 plh3 size-120 p-lr-28 p-tb-25"
                            name="msg" placeholder="How Can We Help?" required></textarea>
                    </div>

                    <?php if (!empty($successMsg)) : ?>
                        <div class="alert alert-info text-center" role="alert">
                            <?= $successMsg ?>
                        </div>
                    <?php endif; ?>

                    <button style="background-color: #321961 !important;" type="submit"
                        class="flex-c-m stext-101 cl0 size-121 bg3 bor1 hov-btn3 p-lr-15 trans-04 pointer">
                        Submit
                    </button>
                </form>
            </div>

            <!-- Contact Info Column -->
            <div class="size-210 bor10 flex-w flex-col-m p-lr-93 p-tb-30 p-lr-15-lg w-full-md">
                
                <div class="flex-w w-full p-b-42">
                    <span class="fs-18 cl5 txt-center size-211">
                        <span style="color: #310E68 !important;" class="lnr lnr-map-marker"></span>
                    </span>
                    <div class="size-212 p-t-2">
                        <span class="mtext-110 cl2">Address</span>
                        <p class="stext-115 cl6 size-213 p-t-18">
                            Office # 101, Building 91-C, 11th Jami Commercial Street D.H.A. Phase 7 Karachi. Pakistan
                        </p>
                    </div>
                </div>

                <div class="flex-w w-full p-b-42">
                    <span class="fs-18 cl5 txt-center size-211">
                        <span style="color: #310E68 !important;" class="lnr lnr-phone-handset"></span>
                    </span>
                    <div class="size-212 p-t-2">
                        <span class="mtext-110 cl2">Lets Talk</span>
                        <p class="stext-115 cl1 size-213 p-t-18">
                            <a href="https://wa.me/923368163677" target="_blank" class="hov-cl1 trans-04">
                                +92 336 8163677
                            </a>
                        </p>
                    </div>
                </div>

                <div class="flex-w w-full">
                    <span class="fs-18 cl5 txt-center size-211">
                        <span style="color: #310E68 !important;" class="lnr lnr-envelope"></span>
                    </span>
                    <div class="size-212 p-t-2">
                        <span class="mtext-110 cl2">Sale Support</span>
                        <p class="stext-115 cl1 size-213 p-t-18">
                            <a href="mailto:Info@scitforte.com" class="hov-cl1 trans-04">
                                Info@scitforte.com
                            </a>
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<?php include("footer.php"); ?>

<!-- Back to top button -->
<div class="btn-back-to-top" id="myBtn">
    <span class="symbol-btn-back-to-top">
        <i class="zmdi zmdi-chevron-up"></i>
    </span>
</div>

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
<script src="vendor/MagnificPopup/jquery.magnific-popup.min.js"></script>
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
