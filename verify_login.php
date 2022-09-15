<?php include "functions.php";
if (!isset($_SESSION['username'])) {
    redirect('signin.php');
} ?>
<?php
if (isset($_POST['submit-otp'])) {
    $otp = $_POST['vcode'];
    $email = $_SESSION['email'];
    if (otpVerification($email, $otp)) {
        if (updateOtp($email, $otp)) {
            redirect('profile.php');
        }
    } else {
        echo "<p style='color:red;'> Incorrect otp entered. Verification failed!</p>";
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="styles/verify_login.css">
    <title>OTP for login.</title>
</head>

<body>

    <div class="container">
        <div class="container">

            <form class="form" id="form" method="POST">
                <h4 style="text-align:center">We have sent verification code to
                    <?php echo isset($_SESSION['email']) ? $_SESSION['email'] : '' ?>. Kindly check your email to
                    login.</h4>
                <u></u>
                <p>Note: Don't forget to check your spam message.</p></u>
                <div class="input-fields">
                    <input type="number" placeholder="Enter OTP" name="vcode">
                </div>
                <input type="submit" value="submit otp" id="button" name="submit-otp">
                <p><a href='signin.php'>Try Again</a></p>
            </form>
        </div>
</body>

</html>