<?php
session_start();
include('connection.php');

// ✅ User ya Guest ID
$user_id = $_SESSION['user_id'] ?? $_SESSION['guest_id'] ?? null;

// ✅ Agar guest_id set nahi hai toh naya generate karo
if (!$user_id) {
    $_SESSION['guest_id'] = rand(100000, 999999);
    $user_id = $_SESSION['guest_id'];
}

// ✅ Wishlist aur Product join karke ek hi query chalao
$q = mysqli_query($con, "
    SELECT p.* FROM wishlist w 
    JOIN products p ON p.id = w.product_id 
    WHERE w.user_id = '$user_id'
");

?>

<!DOCTYPE html>
<html>
<head>
    <title>My Wishlist</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="images/icons/zufelogo-removebg-preview.png"/>
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
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <style>
        .wishlist-card {
            border: 1px solid #ddd; 
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 15px;
        }
        .wishlist-card img {
            max-width: 100%;
            border-radius: 5px;
        }
        
    </style>
</head>
<?php include("header.php"); ?>
<body>
    <?php if (mysqli_num_rows($q) == 0): ?>
        <div class="col-12 d-flex justify-content-center align-items-center mb-5">
    <div class="card shadow-lg border-0" style="max-width: 600px; width: 100%; border-radius: 20px;">
        <div class="card-body text-center p-5">
            <h1 class="card-title mb-3">Nothing In Your Wishlist</h1>
            <h5 class="card-subtitle mb-4 text-muted">We Are Here To Serve You...</h5>
            <a href="index.php" class="btn text-white px-4 py-2" style="background-color: #310E68; border-radius: 30px;">
                Continue Shopping
            </a>
        </div>
    </div>
</div>

    <?php else: ?>
    <h1 class="text-center ltext-103 cl5">Your Wishlist</h1>
    <div class="row container">
        <?php while ($row = mysqli_fetch_assoc($q)) { ?>
            <div class="col-md-3 mb-4">
    <div class="wishlist-card d-flex flex-column justify-content-between" style="height: 100%;">
        <a href="product-detail.php?proid=<?php echo $row['id']; ?>">
            <img src="../adminpanel3/img/<?php echo $row['image']; ?>" alt="Product Image">
        </a>
        <h5 class="mt-2"><b><?php echo $row['name']; ?></b></h5>
        <p>Price: <b>Rs. <?php echo $row['price']; ?></b></p>

        <!-- ✅ Buttons in one line -->
        <div class="d-flex justify-content-between mt-auto">
            <a style="background-color: #310E68 !important; color:white; border-radius: 30px;" href="product-detail.php?proid=<?php echo $row['id']; ?>" class="btn btn-sm w-50 me-1">
               <b>View</b>
            </a>
            <form action="remove-from-wishlist.php" method="POST" onsubmit="return confirm('Are you sure you want to remove this item from wishlist?');" class="w-50">
                <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                <button style="background-color: #9473caff !important; color:white; border-radius: 30px;"  type="submit" class="btn btn-sm w-100"><b>Remove</b></button>
            </form>
        </div>
    </div>
</div>
        <?php } ?>
    <?php endif; ?>

    </div>
        <?php include("footer.php"); ?>
<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
<script src="vendor/animsition/js/animsition.min.js"></script>
<script src="vendor/bootstrap/js/popper.js"></script>
<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="vendor/select2/select2.min.js"></script>
<script>$(".js-select2").select2({minimumResultsForSearch: 20, dropdownParent: $('.dropDownSelect2')});</script>
<script src="vendor/daterangepicker/moment.min.js"></script>
<script src="vendor/daterangepicker/daterangepicker.js"></script>
<script src="vendor/slick/slick.min.js"></script>
<script src="js/slick-custom.js"></script>
<script src="vendor/MagnificPopup/jquery.magnific-popup.min.js"></script>
<script src="vendor/isotope/isotope.pkgd.min.js"></script>
<script src="vendor/sweetalert/sweetalert.min.js"></script>
<script src="vendor/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="js/main.js"></script>

</body>
</html>
