<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'PHPMailer.php';
require_once 'Exception.php';
require_once 'SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['subscribe_email'])) {
    $email = filter_var($_POST['subscribe_email'], FILTER_SANITIZE_EMAIL);

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'jawadulbahar@gmail.com';
            $mail->Password   = 'delwgxougwyvdpzi';
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('jawadulbahar@gmail.com', 'Zufe Website');
            $mail->addAddress('jawadulbahar@gmail.com');

            $mail->isHTML(true);
            $mail->Subject = 'New Newsletter Subscription';
            $mail->Body    = "You have a new subscriber: <strong>$email</strong>";

            $mail->send();
            echo "<script>alert('Thank you for subscribing!');</script>";
        } catch (Exception $e) {
            echo "<script>alert('Subscription failed: {$mail->ErrorInfo}');</script>";
        }
    } else {
        echo "<script>alert('Invalid email address.');</script>";
    }
}
?>
