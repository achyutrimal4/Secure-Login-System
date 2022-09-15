<?php
include 'functions.php';


if (isset($_POST['signup'])) {

    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $_SESSION['username'] = $username;
    $email = trim($_POST['email']);
    $_SESSION['email'] = $email;
    $password = trim($_POST['password1']);
    $passowrd_confirmation = trim($_POST['password2']);

    $secretKey = '6LdqA44gAAAAAP0GnPcf1G0H1r73LH1nMX_Nqx3H';

    // regex to check if the password is strong enough
    $regex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{10,})/";

    //pushing dynamic errors below respective input fields
    $error_type = [
        'fullname' => '',
        'username' => '',
        'email' => '',
        'password' => '',
        'password_confirmation' => ''

    ];

    if ($fullname == '') {
        $error_type['fullname'] = 'This field is required';
    }
    if ($username == '') {
        $error_type['username'] = 'This field is required';
    }
    if (username_exists($username)) {
        $error_type['username'] = 'An user with this username already exists.';
    }

    if ($email == '') {
        $error_type['email'] = 'This field is required';
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_type['email'] = 'Please enter a valid email id.';
    }
    if (email_exists($email)) {
        $error_type['email'] = 'An user with this email address already exists.';
    }
    if (empty($_POST['g-recaptcha-response'])) {
        $empty_captcha = "Captcha cannot be empty.";
    }

    if ($password == '') {
        $error_type['password'] = 'This field is required';
    }
    if ($passowrd_confirmation == '') {
        $error_type['password_confirmation'] = 'This field is required';
    }

    if ($passowrd_confirmation != $password) {
        $error_type['password_confirmation'] = "The two password fields didn't match.";
    }
    
    foreach ($error_type as $key => $value) {
        if (empty($value)) {
            unset($error_type[$key]);
        }
    }
    if (empty($error_type)) {
        if (!preg_match($regex, $password)) {
            $error_type['password'] =  "Password too weak. Please enter a strong password.";
        } else {
            if (preg_match("#(($email)|($username))#", $password)) { //comparing password to email and username.
                $error_type['password'] = 'Password cannot be too similar to username, fullname or email. ';
            } else {
                $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='
                    . $secretKey . '&response=' . $_POST['g-recaptcha-response']);
                $responseData = json_decode($verifyResponse);
                if ($responseData->success) {
                    user_registration($fullname, $username, $email, $password);
                } else {
                    echo "<p style='color:red;>Captcha Verification Failed.</p>";
                }
            }
        }
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
    <title>Sign Up</title>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>Create Account</h2>
            <p>Already have an account? <a href="signin.php">Login</a></p>
        </div>
        <form class="form" id="form" method="POST">

            <!-- input-field Fullname -->
            <div class="input-fields">
                <input type="text" id="fullname" name="fullname" placeholder="Full Name" onclick="hideHelper()">
            </div>
            <p style="font-size: 12px; color: #ff4757; text-align: left; width:100%;  margin-top:-10px; padding-left: 12.5px;">
                <?php echo isset($error_type['fullname']) ? $error_type['fullname'] : '' ?></p>


            <!-- input field Username -->
            <div class="input-fields">
                <input type="text" id="username" name="username" placeholder="Username" onclick="hideHelper()" value="<?php echo isset($_SESSION['username']) ? $_SESSION['username'] : '' ?>">
            </div>
            <p style="font-size: 12px; color: #ff4757; text-align: left; width:100%;  margin-top:-10px; padding-left: 12.5px;">
                <?php echo isset($error_type['username']) ? $error_type['username'] : '' ?></p>

            <!-- input field Email -->
            <div class="input-fields ">
                <input type="text" id="email" name="email" placeholder="Email" onclick="hideHelper()">
            </div>
            <p style="font-size: 12px; color: #ff4757; text-align: left; width:100%;  margin-top:-10px; padding-left: 12.5px;">
                <?php echo isset($error_type['email']) ? $error_type['email'] : '' ?></p>

            <!-- div for password input and helper text -->
            <div class="password-helper">
                <div class="input-fields">
                    <input type="password" name="password1" id="password" placeholder="Password" onkeyup="showHelper()">
                    <span class="eye" onclick="passwordVisibility()">
                        <i id="eye1" class="fas fa-eye"></i>
                        <i id="eye2" class="fas fa-eye-slash"></i>
                    </span>
                    <div class="strength-meter">
                        <span style="position: absolute; top:0">Weak</span>
                        <span></span>
                    </div>
                </div>
                <div class="helper-text">
                    <p><b>Your password must have:</b></p>
                    <p> <i class="fas fa-check-circle" id="check1"></i>At least 8 characters</p>
                    <p><i class="fas fa-check-circle" id="check2"></i>Lowercase letter</p>
                    <p><i class="fas fa-check-circle" id="check3"></i>Uppercase letter</p>
                    <p><i class="fas fa-check-circle" id="check4"></i>Special characters</p>
                    <p><i class="fas fa-check-circle" id="check5"></i>Numeric value</p>

                </div>
            </div>
            <p style="font-size: 12px; color: #ff4757; text-align: left; width:100%;  margin-top:-10px; padding-left: 12.5px;">
                <?php echo isset($error_type['password']) ? $error_type['password'] : '' ?></p>

            <!-- input field for password confirmation -->
            <div class="input-fields">
                <input type="password" id="re-password" name="password2" placeholder="Confirm Password" onclick="hideHelper()">
                <span class="eye-re" onclick="rePasswordVisibility()">
                    <i id="eye3" class="fas fa-eye"></i>
                    <i id="eye4" class="fas fa-eye-slash"></i>
                </span>
            </div>
            <p style="font-size: 12px; color: #ff4757; text-align: left; width:100%;  margin-top:-10px; padding-left: 12.5px;">
                <?php echo isset($error_type['password_confirmation']) ? $error_type['password_confirmation'] : '' ?></p>

            <!-- terms and conditions-->
            <div class="terms">
                <input type="checkbox" id="terms-checkbox" name="terms" value="Bike">
                <label for="terms"> &nbsp; I agree to all the <a href="">terms and conditions.</a></label>
            </div>

            <!-- google recaptcha-->
            <div class="g-recaptcha" data-sitekey="6LdqA44gAAAAAJs8u2-m2gHbdsU6ZJArizZp4M6Y"></div>
            <p style="font-size: 12px; color: red;"><?php echo isset($empty_captcha) ? $empty_captcha : '' ?></p>

            <input type="submit" value="Register" id="button" name="signup">
        </form>
        
    </div>
    <!-- javascript -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <script src="script/script.js"></script>
</body>

</html>