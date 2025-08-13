<?php include('subscribe.php'); ?>
<!-- Footer HTML starts here -->
<footer style="background: linear-gradient(135deg, #211342ff 0%, #814870ff 100%) !important;" class="p-t-75 p-b-32">
    <div class="container">
        <div class="row">
            <div class="col-sm-6 col-lg-3 p-b-50">
                <h4 class="stext-301 cl0 p-b-30">WE ARE ZUFÉ</h4>
                <ul>
                    <li class="p-b-10"><a href="index.php" class="stext-107 cl7 hov-cl1 trans-04">Home</a></li>
                    <li class="p-b-10"><a href="about.php" class="stext-107 cl7 hov-cl1 trans-04">About</a></li>
                    <li class="p-b-10"><a href="contact.php" class="stext-107 cl7 hov-cl1 trans-04">Contact</a></li>
                    <li class="p-b-10"><a href="index.php#cat" class="stext-107 cl7 hov-cl1 trans-04">Categories</a></li>
                </ul>
            </div>

            <div class="col-sm-6 col-lg-3 p-b-50">
                <h4 class="stext-301 cl0 p-b-30">Help</h4>
                <ul>
                    <li class="p-b-10"><a href="thanks.php" class="stext-107 cl7 hov-cl1 trans-04">Your Order List</a></li>
                    <li class="p-b-10"><a href="track_order.php" class="stext-107 cl7 hov-cl1 trans-04">Track Order</a></li>
                    <li class="p-b-10"><a href="returns.php" class="stext-107 cl7 hov-cl1 trans-04">Returns</a></li>
                    <li class="p-b-10"><a href="shipping.php" class="stext-107 cl7 hov-cl1 trans-04">Shipping</a></li>
                    <li class="p-b-10"><a href="faqs.php" class="stext-107 cl7 hov-cl1 trans-04">FAQs</a></li>
                </ul>
            </div>

            <div class="col-sm-6 col-lg-3 p-b-50">
                <h4 class="stext-301 cl0 p-b-30">CATEGORIES</h4>
                <ul>
                    <?php
                    if (!isset($con)) {
                        include('connection.php');
                    }

                    $cats_footer = mysqli_query($con, "SELECT * FROM categories");
                    if ($cats_footer && mysqli_num_rows($cats_footer) > 0) {
                        while ($c = mysqli_fetch_assoc($cats_footer)) {
                            echo "<li class='p-b-10'>
                                    <a href='product.php?id={$c['id']}' class='stext-107 cl7 hov-cl1 trans-04'>" . htmlspecialchars($c['cat_name']) . "</a>
                                </li>";
                        }
                    } else {
                        echo "<li><em style='color:white;'>No categories</em></li>";
                    }
                    ?>
                </ul>
            </div>
            <div class="col-sm-6 col-lg-3 p-b-50">
                <h4 class="stext-301 cl0 p-b-30">Newsletter</h4>
                <form method="POST" action="">
                    <div class="wrap-input1 w-full p-b-4">
                        <input class="input1 bg-none plh1 stext-107 cl7" type="email" name="subscribe_email" placeholder="email@example.com" required>
                        <div class="focus-input1 trans-04"></div>
                    </div>

                    <div class="p-t-18 ">
                        <button type="submit" class="d-flex flex-c-m stext-101 cl5 size-103 bg2 bor1 hov-btn2 p-lr-15 trans-04">
                            Subscribe
                        </button>
                    </div>

                    <div class="p-t-20">
                        &nbsp;<a style="color: #E1306C;" href="https://www.instagram.com" target="_blank" class="cl7 m-r-20 hov-cl1 fs-24">
                            <i class="fa fa-instagram"></i>
                        </a>
                        <a style="color: blue;" href="https://www.facebook.com" target="_blank" class="cl7 m-r-20 hov-cl1 fs-24">
                            <i class="fa fa-facebook"></i>
                        </a>
                        <a style="color: red;" href="https://www.youtube.com" target="_blank" class="cl7 hov-cl1 fs-24">
                            <i class="fa fa-youtube-play"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="p-t-40">
            <div class="flex-c-m flex-w p-b-18">
                <a href="#" class="m-all-1"><img src="images/icons/icon-pay-01.png" alt="ICON-PAY"></a>
                <a href="#" class="m-all-1"><img src="images/icons/icon-pay-02.png" alt="ICON-PAY"></a>
                <a href="#" class="m-all-1"><img src="images/icons/icon-pay-03.png" alt="ICON-PAY"></a>
                <a href="#" class="m-all-1"><img src="images/icons/icon-pay-04.png" alt="ICON-PAY"></a>
                <a href="#" class="m-all-1"><img src="images/icons/icon-pay-05.png" alt="ICON-PAY"></a>
            </div>

           <footer class="text-center p-3">
    <p class=" cl6 txt-center mb-0">
        &copy; Copyrights Reserved by <strong>ZUFÉ</strong> <script>document.write(new Date().getFullYear());</script>
    </p>
</footer>
        </div>
    </div>
</footer>
