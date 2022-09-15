<?php include 'functions.php'; ?>
<!-- session_start(); -->

<?php


if (isset($_POST['signin'])) {
    $username = mysqli_real_escape_string($connection, $_POST['username']);
    $password = mysqli_real_escape_string($connection, $_POST['signin_password']);

    $secretKey = '6LdqA44gAAAAAP0GnPcf1G0H1r73LH1nMX_Nqx3H';

    // validation and push error
    $error_type = [
        'username' => '',
        'password' => ''
    ];
    if ($username == '') {
        $error_type['username'] = 'This field is required.';
    }

    if ($password == '') {
        $error_type['password'] = 'This field is required.';
    }
    if (empty($_POST['g-recaptcha-response'])) {
        $empty_captcha = "Captcha cannot be empty.";
    }

    $db_userdata = " select * from user where username = '$username'";
    $user_query = mysqli_query($connection, $db_userdata);
    $usercount = mysqli_num_rows($user_query);

    // check if user exists in the database
    if ($usercount) {
        $userdata = mysqli_fetch_array($user_query);
        $email = $userdata['email'];
        if (isEmailVerified($email)) {
            echo "<p style='color:#f53b3b;'>
            Please verify your email to continue.</p>";
            sendOtpEmail($email);
        } else {
            foreach ($error_type as $key => $value) {
                if (empty($value)) {
                    unset($error_type[$key]);
                }
            }
            if (empty($error_type)) {
                $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='
                    . $secretKey . '&response=' . $_POST['g-recaptcha-response']);
                $responseData = json_decode($verifyResponse);
                if ($responseData->success) {
                    user_login($username, $password);
                } else {
                    echo "<p style='color:red;>Captcha Verification Failed.</p>";
                }
               
            }
        }
    } else {
        echo "<p style='color:#f53b3b;'>
            Incorrect username or password.</p>";
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
    <link rel="stylesheet" type="text/css" href="styles/style.css">
    <title>Sign In</title>
    <!-- <link rel="icon" type="image/x-icon" href="icon.svg"> -->
</head>

<body>
    <!-- session message from password reset and change password -->
    <div class="success-flash">
        <?php if (isset($_SESSION['link_sent_msg'])) {
            echo $_SESSION['link_sent_msg'];
            unset($_SESSION['link_sent_msg']);
        } ?>
        <?php if (isset($_SESSION['reset_success_msg'])) {
            echo $_SESSION['reset_success_msg'];
            unset($_SESSION['reset_success_msg']);
        } ?>
    </div>

    <div class="container">
        <div class="header">
            <h2>Login</h2>
            <p>Don't have an account? <a href="signup.php">Signup</a></p>
        </div>
        <form class="form" id="form" method="POST" action="<?php echo htmlentities($_SERVER['PHP_SELF']) ?>" novalidate>

            <h2>Advanced Cyber Security</h2>

            <p>Login to continue!</p>

            <div class="input-fields ">
                <input type="text" id="username" name="username" placeholder="Username" onclick="hideHelper()" value="<?php echo isset($username) ? $username : '' ?>">
                <small>Message</small>
            </div>
            <p id="error-msg-email" style="font-size: 12px; color: #ff4757; text-align: left; width:100%;  margin-top:-10px; padding-left: 12.5px;">
                <?php echo isset($error_type['username']) ? $error_type['username'] : '' ?></p>

            <div class="input-fields">
                <input type="password" id="signin_password" name="signin_password" placeholder=" Password">
                <span class="eye-signin" onclick="signinPasswordVisibility()">
                    <i id="eye-signin" class="fas fa-eye"></i>
                    <i id="eye-signin2" class="fas fa-eye-slash"></i>
                </span>
            </div>
        

            <p style="font-size: 12px; color: #ff4757; text-align: left; width:100%;  margin-top:-10px; padding-left: 12.5px;">
                <?php echo isset($error_type['password']) ? $error_type['password'] : '' ?></p>

                <div class="g-recaptcha" data-sitekey="6LdqA44gAAAAAJs8u2-m2gHbdsU6ZJArizZp4M6Y"></div>
            <p style="font-size: 12px; color: red;"><?php echo isset($empty_captcha) ? $empty_captcha : '' ?></p>

            <input type="submit" value="Login" id="button" name="signin">
            <br>
            <a href="forgot-password.php">Forgot Password?</a>

        </form>
    </div>
    <script src="script/script.js"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</body>

</html>