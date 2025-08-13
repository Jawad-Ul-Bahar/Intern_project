<?php
include("query.php");
include("header.php")
?>



<!DOCTYPE html>
<html lang="en">

<head>
	<title>Shoping Cart</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!--===============================================================================================-->
	<link rel="icon" type="image/png" href="images/icons/favicon.png" />
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/iconic/css/material-design-iconic-font.min.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/linearicons-v1.0.0/icon-font.min.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animsition/css/animsition.min.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/perfect-scrollbar/perfect-scrollbar.css">
	<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="css/util.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
	<!--===============================================================================================-->
</head>

<body class="animsition">



	<!-- breadcrumb -->
	<div class="container">
		<div class="bread-crumb flex-w p-l-25 p-r-15 p-t-30 p-lr-0-lg">
			<a href="index.html" class="stext-109 cl8 hov-cl1 trans-04">
				Home
				<i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
			</a>

			<span class="stext-109 cl4">
				Shoping Cart
			</span>
		</div>
	</div>


	<!-- Shoping Cart -->
	<form class="bg0 p-t-75 p-b-85" method="post" action="checkout">
		<div class="container">
			<div class="row">
				<div class="col-lg-10 col-xl-7 m-lr-auto m-b-50">
					<div class="m-l-25 m-r--38 m-lr-0-xl">
						<div class="wrap-table-shopping-cart">
							<table class="table-shopping-cart">
								<tr class="table_head">
									<th class="column-1">Product</th>
									<th class="column-2">Name</th>
									<th class="column-3">Price</th>
									<th class="column-4">Quantity</th>
									<th class="column-5">Total</th>
								</tr>
								<?php
								$total = 0;
								$alltotal = 0;
								foreach($_SESSION['cart'] as $key => $val) {
									$total = $val['proqty'] *$val['proprice'];
									$alltotal += $total;
								
								?>
									<tr class="table_row">
										<td class="column-1">
	
											<div class="how-itemcart1">
												<img src="../adminpanel3/img/<?php echo $val['proimg']; ?>" alt="IMG">
											</div>
										</td>
										<td class="column-2"><?php echo $val['proname']; ?></td>
										<td class="column-3"><?php echo $val['proprice']; ?></td>
										<td class="column-4"><?php echo $val['proqty'] ?> </td>
										<td class="column-5"><?php  echo $total ?></td>
										<?php
								}
										?>
									</tr>

							</table>
						</div>

						<div class="flex-w flex-sb-m bor15 p-t-18 p-b-15 p-lr-40 p-lr-15-sm">
							<div class="flex-w flex-m m-r-20 m-tb-5">
								<input class="stext-104 cl2 plh4 size-117 bor13 p-lr-20 m-r-10 m-tb-5" type="text" name="coupon" placeholder="Coupon Code">

								<div class="flex-c-m stext-101 cl2 size-118 bg8 bor13 hov-btn3 p-lr-15 trans-04 pointer m-tb-5">
									Apply coupon
								</div>
							</div>

							<div class="flex-c-m stext-101 cl2 size-119 bg8 bor13 hov-btn3 p-lr-15 trans-04 pointer m-tb-10">
								Update Cart
							</div>
						</div>
					</div>
				</div>

				<div class="col-sm-10 col-lg-7 col-xl-5 m-lr-auto m-b-50">
					<div class="bor10 p-lr-40 p-t-30 p-b-40 m-l-63 m-r-40 m-lr-0-xl p-lr-15-sm">
						<h4 class="mtext-109 cl2 p-b-30">
							Cart Totals
						</h4>

						<div class="flex-w flex-t bor12 p-b-13">
							<div class="size-208">
								<span class="stext-110 cl2">
									Subtotal:
								</span>
							</div>

							<div class="size-209">
								<span class="mtext-110 cl2">
									<?php

										 echo $alltotal
          
									?>
								</span>
							</div>
						</div>

						<div class="flex-w flex-t bor12 p-t-15 p-b-30">
							<div class="size-208 w-full-ssm">
								<span class="stext-110 cl2">
									Shipping:
								</span>
							</div>

							<div class="size-209 p-r-18 p-r-0-sm w-full-ssm">
								<p class="stext-111 cl6 p-t-2">
									There are no shipping methods available. Please double check your address, or contact us if you need any help.
								</p>

    <div class="p-t-15">
        <span class="stext-112 cl8">Personal Information</span>

        <div class="bor8 bg0 m-b-12">
            <input class="stext-111 cl8 plh3 size-111 p-lr-15" type="text" name="name" placeholder="Name" required>
        </div>

        <div class="bor8 bg0 m-b-12">
            <input class="stext-111 cl8 plh3 size-111 p-lr-15" type="text" name="address" placeholder="Address" required>
        </div>

        <div class="bor8 bg0 m-b-12">
            <input class="stext-111 cl8 plh3 size-111 p-lr-15" type="email" name="email" placeholder="E-mail" required>
        </div>

        <div class="bor8 bg0 m-b-12">
            <input class="stext-111 cl8 plh3 size-111 p-lr-15" type="tel" name="work_phone" placeholder="Work Phone No." required>
        </div>

        <div class="bor8 bg0 m-b-12">
            <input class="stext-111 cl8 plh3 size-111 p-lr-15" type="tel" name="cell_phone" placeholder="Cell No." required>
        </div>

        <div class="bor8 bg0 m-b-12">
            <input class="stext-111 cl8 plh3 size-111 p-lr-15" type="date" name="dob" placeholder="Date Of Birth" required>
        </div>
	<div class="rs1-select2 rs2-select2 bor8 bg0 m-b-12 m-t-9">
    <select class="js-select2" name="categroy" required>
    <option>Select Categroy</option>
    <?php
    // Loop through each item in the cart
    foreach ($_SESSION['cart'] as $key => $val) {
        $proid = $val['proid'];
        $query = mysqli_query($con, "SELECT * FROM `products` WHERE id='$proid'");
        $fetch = mysqli_fetch_assoc($query);
        $cat_id = $fetch['cat_id'];
        
        // Fetch category data
        $q1 = mysqli_query($con, "SELECT * FROM `categories` WHERE id='$cat_id'");
        $cat = mysqli_fetch_assoc($q1);
        
        // Product and category names
        $product_name = htmlspecialchars($fetch['name']);
        $category_name = htmlspecialchars($cat['cat_name']);
        
        // Option value will be the product ID, but the text shows both product and category names
        echo "<option value='" . $cat_id . "'>" .  $category_name . "</option>";
    }
    ?>
</select>

    <div class="dropDownSelect2"></div>
</div>
       <div class="bor8 bg0 m-b-22">
            <input class="stext-111 cl8 plh3 size-111 p-lr-15" type="text" name="remarks" placeholder="Remarks (other additional informational)">
        </div>
	
        <div class="flex-w">
            <div class="flex-c-m stext-101 cl2 size-115 bg8 bor13 hov-btn3 p-lr-15 trans-04 pointer">
                <!-- <button type="submit" name="btn_order">Submit Order</button> -->
            </div>
        </div>
    </div>
<?php

session_start();  // Start the session to get the user session data
include('db_connection.php');  // Make sure to include your DB connection

if (isset($_GET['checkout'])) {
    // Get session data
    $uid = $_SESSION['userid'];   // User ID from session
    $uname = $_SESSION['username'];  // Username from session

    // Get form data
    $name = $_POST['name'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $work_phone = $_POST['work_phone'];
    $cell_phone = $_POST['cell_phone'];
    $dob = $_POST['dob'];
    $category = $_POST['category'];  // Corrected spelling
    $remarks = $_POST['remarks'];

    // Assuming the product info comes from session or elsewhere
    $pid = $_SESSION['proid'];  // Product ID (you would need to assign this)
    $pname = $_SESSION['proname'];  // Product Name (you would need to assign this)
    $price = $_SESSION['proprice'];  // Product Price (you would need to assign this)
    $pqty = $_SESSION['proqty'];  // Product Quantity (you would need to assign this)

    // Insert each product order separately
    $myQuery = mysqli_query($con, "INSERT INTO orders 
        (`uid`, `uname`, `proid`, `proname`, `proprice`, `proqty`, `name`, `address`, `email`, `work_phone`, `cell_phone`, `dob`, `category`, `remarks`) 
        VALUES ('$uid', '$uname', '$pid', '$pname', '$price', '$pqty', '$name', '$address', '$email', '$work_phone', '$cell_phone', '$dob', '$category', '$remarks')");

    // Check if the query was successful
    if ($myQuery) {
        echo "<script>alert('Order placed successfully!');</script>";
        header("Location: order_confirmation.php");  // Redirect to confirmation page (optional)
    } else {
        echo "<script>alert('Error placing order. Please try again later.');</script>";
    }
}
?>


								</div>
							</div>
						</div>

						<div class="flex-w flex-t p-t-27 p-b-33">
							<div class="size-208">
								<span class="mtext-101 cl2">
									Total:   <?php echo  $alltotal ?>
								</span>
							</div>

							<div class="size-209 p-t-1">
								<span class="mtext-110 cl2">
									<br>
									<input type="hidden" value='<?php echo $_SESSION['userid']?>'>

								</span>
							</div>
						</div>
							<?php
							if(isset($_SESSION['username'])){

							?>
						<a href="?checkout" class="flex-c-m stext-101 cl0 size-116 bg3 bor14 hov-btn3 p-lr-15 trans-04 pointer">
							Proceed to Checkout 
							<!-- <?php echo $_SESSION['userid']?> -->
							<input type="hidden" value='<?php echo $_SESSION['username']?>'>
						</a>
						<?php
						}
						else{
							?>
								<a href="login.php" class="flex-c-m stext-101 cl0 size-116 bg3 bor14 hov-btn3 p-lr-15 trans-04 pointer">
							    Proceed to Checkout
						</a>
							<?php
						}
						?>

					
					</div>
				</div>
			</div>
		</div>

	</form>
	<?php



?>
	



<?php
include('foo')
?>

	<!--===============================================================================================-->
	<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
	<!--===============================================================================================-->
	<script src="vendor/animsition/js/animsition.min.js"></script>
	<!--===============================================================================================-->
	<script src="vendor/bootstrap/js/popper.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
	<!--===============================================================================================-->
	<script src="vendor/select2/select2.min.js"></script>
	<script>
		$(".js-select2").each(function() {
			$(this).select2({
				minimumResultsForSearch: 20,
				dropdownParent: $(this).next('.dropDownSelect2')
			});
		})
	</script>
	<!--===============================================================================================-->
	<script src="vendor/MagnificPopup/jquery.magnific-popup.min.js"></script>
	<!--===============================================================================================-->
	<script src="vendor/perfect-scrollbar/perfect-scrollbar.min.js"></script>
	<script>
		$('.js-pscroll').each(function() {
			$(this).css('position', 'relative');
			$(this).css('overflow', 'hidden');
			var ps = new PerfectScrollbar(this, {
				wheelSpeed: 1,
				scrollingThreshold: 1000,
				wheelPropagation: false,
			});

			$(window).on('resize', function() {
				ps.update();
			})
		});
	</script>
	<!--===============================================================================================-->
	<script src="js/main.js"></script>

</body>

</html>