<?php 
include("connection.php");
session_start();

if(isset($_SESSION['username'])!=null){
    echo "<script>location.assign('index.php')</script>";
} else {
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Admin Login</title>

    <!-- Fonts & CSS -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Custom Styling -->
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            background-image: url('../theme/images/BG-L2.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;

            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.48);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            padding: 40px 30px;
            max-width: 450px;
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
            .login-card {
                padding: 30px 20px;
            }
        }
    </style>
</head>

<body>

    <div class="login-card text-center">
        <h3 class="text-gray-900 mb-4">Login Here!</h3>

        <form class="user" method="post">
            <div class="form-group">
                <input type="email" name="email" class="form-control form-control-user" placeholder="Enter Email Address..." required>
            </div>

            <div class="form-group">
                <input type="password" name="password" class="form-control form-control-user" placeholder="Password" required>
            </div>

            <div class="form-group text-left">
                <div class="custom-control custom-checkbox small">
                    <input type="checkbox" class="custom-control-input" id="customCheck">
                    <label class="custom-control-label" for="customCheck">Remember Me</label>
                </div>
            </div>

            <input type="submit" value="Login" class="btn btn-primary btn-user btn-block" name="btn_login">

            <hr>
            <a href="#" class="btn btn-google btn-user btn-block">
                <i class="fab fa-google fa-fw"></i> Login with Google
            </a>
            <a href="#" class="btn btn-facebook btn-user btn-block">
                <i class="fab fa-facebook-f fa-fw"></i> Login with Facebook
            </a>
        </form>

        <?php 
        if(isset($_POST['btn_login'])){
            $email = $_POST['email'];
            $password = $_POST['password'];

            $q = mysqli_query($con, "SELECT * FROM `users` WHERE email='$email' AND pass='$password'");
            $login = mysqli_num_rows($q);
            $user = mysqli_fetch_array($q);

            if($login){
                $_SESSION['username'] = $user[1];
                $_SESSION['userrole'] = $user[5];

                echo "<script>alert('Login successfully'); location.assign('index.php')</script>";
            } else {
                echo "<script>alert('Login failed')</script>";
            }
        }
        ?>

        <hr>
        <div class="text-center">
            <a class="small" href="forgot-password.html">Forgot Password?</a>
        </div>
        <div class="text-center">
            <a class="small" href="register.php">Create an Account!</a>
        </div>
    </div>

    <!-- Scripts -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>

</body>

</html>
<?php } ?>
