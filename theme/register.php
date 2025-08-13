<?php
include("query.php")
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="images/icons/zufelogo-removebg-preview.png"/>

    <title>Zufe Register</title>

    <!-- Fonts & Icons -->
    <link href="../adminpanel3/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">

    <!-- CSS -->
    <link href="../adminpanel3/css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            background-image: url('../theme/images/BG-L2.png'); /* Same as login */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .register-card {
            background: rgba(255, 255, 255, 0.48);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            padding: 40px 30px;
            max-width: 500px;
            width: 100%;
        }

        .form-control-user {
            border-radius: 8px;
            padding: 15px;
        }

        .btn-user {
            border-radius: 8px;
            padding: 12px;
            font-weight: 600;
        }

        @media (max-width: 480px) {
            .register-card {
                padding: 30px 20px;
            }
        }
    </style>
</head>

<body>

    <div class="register-card text-center">
        <h3 class="text-gray-900 mb-4">Create Your Account!</h3>

        <form class="user" method="post">
            <div class="form-group row">
                <div class="col-sm-6 mb-3 mb-sm-0">
                    <input type="text" class="form-control form-control-user" name="fname" placeholder="First Name" required>
                </div>
                <div class="col-sm-6">
                    <input type="text" class="form-control form-control-user" name="lname" placeholder="Last Name" required>
                </div>
            </div>

            <div class="form-group">
                <input type="email" class="form-control form-control-user" name="email" placeholder="Email Address" required>
            </div>

            <div class="form-group row">
                <div class="col-sm-6 mb-3 mb-sm-0">
                    <input type="password" class="form-control form-control-user" name="password" placeholder="Password" required>
                </div>
                <div class="col-sm-6">
                    <input type="password" class="form-control form-control-user" placeholder="Repeat Password" required>
                </div>
            </div>

            <input style="background-color: #310E68 !important;" type="submit" value="Register Account" class="btn text-light btn-user btn-block" name="btn_register">

            <hr>
            <a href="#" class="btn btn-google btn-user btn-block">
                <i class="fab fa-google fa-fw"></i> Register with Google
            </a>
            <a href="#" class="btn btn-facebook btn-user btn-block">
                <i class="fab fa-facebook-f fa-fw"></i> Register with Facebook
            </a>
        </form>

       <?php 
        if(isset($_POST['btn_register'])){
            $fname=$_POST['fname'];
            $lname=$_POST['lname'];
            $email=$_POST['email'];
            $password=$_POST['password'];

            $q = mysqli_query($con,"INSERT INTO `users`(`fname`, `lname`, `email`, `pass`, `role`) 
                                    VALUES ('$fname','$lname','$email','$password', 'user')");

            if($q){
                echo "<script>alert('registered successfully'); location.assign('login.php');</script>";
            } else {
                echo "<script>alert('error')</script>";
            }
        }
        ?>


        <hr>

        <div class="text-center">
            <a class="small" href="login.php">Already have an account? Login!</a>
        </div>
    </div>

    <!-- Scripts -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>

</body>
</html>
