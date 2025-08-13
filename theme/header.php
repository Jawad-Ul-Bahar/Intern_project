<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once("connection.php");

// Correct logic – only run once
$user_id = $_SESSION['user_id'] ?? $_SESSION['guest_id'] ?? null;
$wishlist_count = 0;
if ($user_id) {
    $wres = mysqli_query($con, "SELECT COUNT(*) as total FROM wishlist WHERE user_id = '$user_id'");
    $wishlist_count = mysqli_fetch_assoc($wres)['total'];
}
// Your Orders count (jo placed nahi hue hain, yani cart mein hain DB mein)
$orders_count = 0;
$session_id = session_id();
$uid = $_SESSION['user_id'] ?? 0;

$oQuery = mysqli_query($con, "SELECT COUNT(*) as total FROM cart WHERE tracking_number IS NULL AND (uid = '$uid' OR session_id = '$session_id')");
if ($oQuery) {
    $orders_count = mysqli_fetch_assoc($oQuery)['total'];
}
?>

<style>
    .logo-mobile img {
    height: 100% !important; /* or any size you want */
    width: auto;  /* keep aspect ratio */
}

    /* Desktop view */
@media (min-width: 992px) {
    .wrap-header-mobile {
        display: none !important;
    }
}

/* Mobile view */
@media (max-width: 991px) {
    .container-menu-desktop {
        display: none !important;
    }
}

.sub-menu-m {
    display: none;
}
.sub-menu-m.open-submenu {
    display: block;
}

/* Remove button on cart item */
.remove-btn {
    position: absolute;
    top: 5px;
    right: 10px;
    color: red;
    font-size: 20px;
    font-weight: bold;
    text-decoration: none;
    z-index: 10;
}

/* Hover effect for remove icon */
.header-cart-item {
    position: relative;
}

/* Always visible remove icon at the right */
.remove-from-cart-icon {
    font-size: 18px;
    color: red;
    margin-left: 10px;
    cursor: pointer;
}

.header-cart-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.limiter-menu-desktop.container {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.menu-desktop {
    flex-grow: 1;
    text-align: center;
}

.wrap-icon-header {
    margin-left: auto;
    display: flex;
    align-items: center;
}

/* Desktop header - single definition */
.container-menu-desktop {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    background-color: #fff;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
}

.container-menu-desktop.scrolled {
    background-color: rgba(255, 255, 255, 0.7);
    box-shadow: none;
}

/* Mobile header - make it fixed at top */
.wrap-header-mobile {
    position: fixed !important;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    background-color: #fff;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    padding: 10px 15px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
}

.wrap-header-mobile.scrolled {
    background-color: rgba(255, 255, 255, 0.95);
    box-shadow: none;
}

/* Mobile menu positioning */
.menu-mobile {
    position: fixed;
    top: 60px;
    left: 0;
    right: 0;
    z-index: 999;
    background-color: #fff;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    max-height: calc(100vh - 60px);
    overflow-y: auto;
}

/* Body padding - responsive */
body {
    padding-top: 130px; /* Desktop default */
}

@media (max-width: 991px) {
    body {
        padding-top: 70px; /* Mobile */
    }
}

/* Mobile header alignment */
.wrap-header-mobile .logo-mobile {
    flex-shrink: 0;
}

.wrap-header-mobile .wrap-icon-header {
    flex-shrink: 0;
}

.wrap-header-mobile .btn-show-menu-mobile {
    flex-shrink: 0;
}

</style>

<header>
    <!-- Header desktop -->
    <div class="container-menu-desktop">
        <div class="limiter-menu-desktop">
            <!-- Logo desktop -->
            <a href="index.php" class="logo">
                <img src="images/icons/zufelogo-removebg-preview.png" alt="Logo" style="height: 130px;">
            </a>

            <!-- Navigation menu -->
            <ul class="main-menu">
                <li class="active-menu"><a href="index.php">Home</a></li>
                <li><a href="about.php">About</a></li>

                <li class="dropdown">
                    <a href="#">Categories</a>
                    <ul class="sub-menu">
                        <?php
                        if (!isset($con)) {
                            include('connection.php');
                        }

                        $cats = mysqli_query($con, "SELECT * FROM categories");
                        if ($cats && mysqli_num_rows($cats) > 0) {
                            while ($c = mysqli_fetch_assoc($cats)) {
                                echo "<li><a href='product.php?id={$c['id']}'>" . htmlspecialchars($c['cat_name']) . "</a></li>";
                            }
                        } else {
                            echo "<li><em>No categories</em></li>";
                        }
                        ?>
                    </ul>
                </li>

                <li><a href="contact.php">Contact</a></li>
            </ul>

            <!-- Header icons (search, cart, wishlist, profile) -->
            <div class="wrap-icon-header flex-w flex-r-m">
                <!-- Search icon -->
                <div class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 js-show-modal-search">
                    <i class="zmdi zmdi-search"></i>
                </div>

                <!-- Cart icon with count -->
                <div class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 icon-header-noti js-show-cart" data-notify="<?php
                    echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : '0';
                ?>">
                    <i class="zmdi zmdi-shopping-cart"></i>
                </div>

                <!-- Wishlist icon -->
                <a href="wishlist-view.php" class="dis-block icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 icon-header-noti" data-notify="<?php echo $wishlist_count; ?>">
                    <i class="zmdi zmdi-favorite-outline"></i>
                </a>

                <!-- Orders icon -->
                <a href="your_orders.php" class="dis-block icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 icon-header-noti" data-notify="<?php echo $orders_count; ?>">
                    <i class="zmdi zmdi-case-check"></i>
                </a>

                <!-- Profile dropdown -->
                <div class="dropdown">
                    <a href="#" id="profileDropdown"
                    class="dis-block icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 dropdown-toggle"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="zmdi zmdi-account-circle"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="profileDropdown">
                        <?php if (isset($_SESSION['username'])): ?>
                            <a class="dropdown-item" href="logout.php"><i class="zmdi zmdi-power mr-2"></i> Logout</a>
                        <?php else: ?>
                            <a class="dropdown-item" href="login.php"><i class="zmdi zmdi-account mr-2"></i> Login</a>
                            <a class="dropdown-item" href="register.php"><i class="zmdi zmdi-account-add mr-2"></i> Register</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Header mobile -->
    <div class="wrap-header-mobile">
        <!-- Logo mobile -->
        <div class="logo-mobile">
            <a href="index.php"><img src="images/icons/zufelogo-removebg-preview.png" alt="IMG-LOGO"></a>
        </div>

        <!-- Icons mobile -->
        <div class="wrap-icon-header flex-w flex-r-m m-r-15">
            <div class="icon-header-item cl2 hov-cl1 trans-04 p-r-11 js-show-modal-search">
                <i class="zmdi zmdi-search"></i>
            </div>

            <div class="icon-header-item cl2 hov-cl1 trans-04 p-r-11 p-l-10 icon-header-noti js-show-cart"
                data-notify="<?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : '0'; ?>">
                <i class="zmdi zmdi-shopping-cart"></i>
            </div>

            <a href="wishlist-view.php" class="dis-block icon-header-item cl2 hov-cl1 trans-04 p-r-11 p-l-10 icon-header-noti" data-notify="<?php echo $wishlist_count; ?>">
                <i class="zmdi zmdi-favorite-outline"></i>
            </a>

            <!-- Orders icon -->
            <a href="your_orders.php" class="dis-block icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 icon-header-noti" data-notify="<?php echo $orders_count; ?>">
                <i class="zmdi zmdi-case-check"></i>
            </a>
        </div>

        <!-- Mobile menu toggle button -->
        <div class="btn-show-menu-mobile hamburger hamburger--squeeze">
            <span class="hamburger-box">
                <span class="hamburger-inner"></span>
            </span>
        </div>
    </div>

    <!-- Mobile menu items -->
    <div class="menu-mobile">
        <ul class="main-menu-m">
            <li><a href="index.php">Home</a></li>
            <li><a href="about.php">About</a></li>

            <li>
                <a href="#">Categories</a>
                <ul class="sub-menu-m">
                    <?php
                    // Connection already included above in your full code
                    $cats = mysqli_query($con, "SELECT * FROM categories");
                    if ($cats && mysqli_num_rows($cats) > 0) {
                        while ($c = mysqli_fetch_assoc($cats)) {
                            echo "<li><a href='product.php?id={$c['id']}'>" . htmlspecialchars($c['cat_name']) . "</a></li>";
                        }
                    } else {
                        echo "<li><em>No categories</em></li>";
                    }
                    ?>
                </ul>
            </li>

            <li><a href="contact.php">Contact</a></li>

            <?php if (isset($_SESSION['username'])): ?>
                <li><a href="logout.php"><i class="zmdi zmdi-power mr-2"></i> Logout</a></li>
            <?php else: ?>
                <li><a href="login.php"><i class="zmdi zmdi-account mr-2"></i> Login</a></li>
                <li><a href="register.php"><i class="zmdi zmdi-account-add mr-2"></i> Register</a></li>
            <?php endif; ?>
        </ul>
    </div>

    <!-- Modal search form -->
    <div class="modal-search-header flex-c-m trans-04 js-hide-modal-search">
        <div class="container-search-header">
            <button class="flex-c-m btn-hide-modal-search trans-04 js-hide-modal-search">
                <img src="images/icons/icon-close2.png" alt="CLOSE">
            </button>
            <form class="wrap-search-header flex-w p-l-15" action="product.php" method="GET">
                <button class="flex-c-m trans-04"><i class="zmdi zmdi-search"></i></button>
                <input class="plh3" type="text" name="search" placeholder="Search...">
            </form>
        </div>
    </div>
</header>

<!-- Cart panel -->
<div class="wrap-header-cart js-panel-cart">
    <div class="s-full js-hide-cart"></div>

    <div class="header-cart flex-col-l p-l-65 p-r-25">

        <!-- Cart title -->
        <div class="header-cart-title flex-w flex-sb-m p-b-8">
            <span class="mtext-103 cl2">Your Cart</span>
            <div class="fs-35 lh-10 cl2 p-lr-5 pointer hov-cl1 trans-04 js-hide-cart">
                <i class="zmdi zmdi-close"></i>
            </div>
        </div>

        <!-- Cart items -->
        <div class="header-cart-scroll" style="max-height: 300px; overflow-y: auto;">
            <ul class="header-cart-wrapitem w-full">
                <?php
                $count = 0;
                $total = 0;
                if (isset($_SESSION['cart'])) {
                    foreach ($_SESSION['cart'] as $key => $val) {
                        $total = $val['proqty'] * $val['proprice'];
                        $count += $total;
                ?>
               <li class="header-cart-item d-flex align-items-center m-b-12" style="width: 100%;">
                    <!-- Left: Image + Text -->
                    <div class="d-flex align-items-center" style="flex: 1;">
                        <!-- Product Image -->
                        <div class="header-cart-item-img me-3">
                            <img src="../adminpanel3/img/<?php echo $val['proimg']; ?>" alt="IMG" class="img-fluid" style="width: 60px; height: auto;">
                        </div>

                        <!-- Product Info -->
                        <div class="header-cart-item-txt">
                            <a href="#" class="header-cart-item-name m-b-18 hov-cl1 trans-04 d-block" style="font-size: 14px;">
                                <?php echo $val['proname']; ?>
                            </a>
                            <span class="header-cart-item-info" style="font-size: 12px; color: #888;">
                                <?php echo $val['proqty'] . " x " . $val['proprice']; ?>
                            </span>
                        </div>
                    </div>

                    <!-- Right: Remove Icon -->
                    <a href="#" class="remove-from-cart text-danger ms-auto" data-index="<?php echo $key; ?>" title="Remove item" style="padding-left: 100px;">
                        <i class="zmdi zmdi-delete" style="font-size: 20px;"></i>
                    </a>
                    </li>
                <?php
                    }
                } else {
                ?>
                <li class="header-cart-item">Your cart is empty.</li>
                <?php } ?>
            </ul>
        </div>

        <!-- Cart total and buttons -->
        <div class="w-full">
            <div class="header-cart-total w-full p-tb-40">
                Total: RS <?php echo $count; ?>
            </div>

            <div class="header-cart-buttons flex-w w-full">
                <?php if (!empty($_SESSION['cart'])): ?>
                <a href="your_orders.php" class="flex-c-m stext-101 cl0 size-107 bg3 bor2 hov-btn3 p-lr-15 trans-04 m-b-10">View Cart</a>
                <a href="shoping-cart.php" class="flex-c-m stext-101 cl0 size-107 bg3 bor2 hov-btn3 p-lr-15 trans-04 m-b-10" name="checkout">Check Out</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Single optimized JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Cart item removal
    document.querySelectorAll('.remove-from-cart').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const index = this.dataset.index;

            fetch('remove-from-cart.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `index=${index}`
            })
            .then(res => res.text())
            .then(data => {
                if (data.trim() === "success") {
                    const item = this.closest('.header-cart-item');
                    if (item) item.remove();
                    location.reload();
                } else {
                    alert("Failed to remove item.");
                }
            });
        });
    });

    // Mobile menu toggle
    const mobileMenuLinks = document.querySelectorAll('.main-menu-m > li > a');
    mobileMenuLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            const submenu = this.nextElementSibling;
            if (submenu && submenu.classList.contains('sub-menu-m')) {
                e.preventDefault();
                submenu.classList.toggle('open-submenu');
            }
        });
    });

    // Single scroll handler for both desktop and mobile headers
    const desktopHeader = document.querySelector('.container-menu-desktop');
    const mobileHeader = document.querySelector('.wrap-header-mobile');

    window.addEventListener('scroll', function () {
        if (window.scrollY > 50) {
            if (desktopHeader) desktopHeader.classList.add('scrolled');
            if (mobileHeader) mobileHeader.classList.add('scrolled');
        } else {
            if (desktopHeader) desktopHeader.classList.remove('scrolled');
            if (mobileHeader) mobileHeader.classList.remove('scrolled');
        }
    });
});
</script>