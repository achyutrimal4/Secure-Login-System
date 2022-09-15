<?php include 'functions.php';
// if (!isset($_SESSION['username'])) {
//     redirect('signin.php');
// } ?>

<?php
if (isset($_POST['reset-password-btn'])) {

    // check if user exists using user token
    if (isset($_GET['token'])) {
        $token = $_GET['token'];
        $newpassword = trim($_POST['password1']);
        $passowrd_confirmation = trim($_POST['password2']);
        $pass1 = password_hash($newpassword, PASSWORD_BCRYPT);
        $pass2 = password_hash($passowrd_confirmation, PASSWORD_BCRYPT);


        // validate input and push errors if any
        $error_type = [

            'password' => '',
            'password_confirmation' => ''

        ];
        if ($newpassword == '') {
            $error_type['password'] = 'This field is required';
        }
        if ($passowrd_confirmation == '') {
            $error_type['password_confirmation'] = 'This field is required';
        }
        foreach ($error_type as $key => $value) {
            if (empty($value)) {
                unset($error_type[$key]);
            }
        }
        if (empty($error_type)) {

            if ($newpassword === $passowrd_confirmation) {

                $updatequery = "update user set password = '$pass1' where token='$token'";
                $query = mysqli_query($connection, $updatequery);

                if ($query) {
                    $_SESSION['reset_success_msg'] = "Password successfully reset. Login to continue";
                    header('location:signin.php');
                } else {
                    echo "<p style='color:red;'> Error occured. Password could not be changed.</p>";
                    redirect('reset-password.php');
                }
            } else {
                echo "<p style='color:red;'>Error! The two password fields do not match.</p>";
            }
        } else {
            echo "<p style='color:red;'> Error occured. Password could not be changed.</p>";
        }
    } else {
        echo "<p style='color:red;'>No valid information provided.</p>";
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
    <title>Reset your password</title>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>Create a new password.</h2>
        </div>
        <form class="form" id="form" method="POST" action="" novalidate>
            <h3>Create a strong password.</h3>
            <!-- div for password input and helper text -->
            <div class="password-helper">
                <div class="input-fields">
                    <input type="password" name="password1" id="password" placeholder="New Password" onkeyup="showHelper()">
                    <span class="eye" onclick="passwordVisibility()">
                        <i id="eye1" class="fas fa-eye"></i>
                        <i id="eye2" class="fas fa-eye-slash"></i>
                    </span>
                    <!-- password strength meter -->
                    <div class="strength-meter">
                        <span style="position: absolute; top:0">Weak</span>
                        <span></span>
                    </div>
                </div>
                <div class="helper-text">
                    <p><b>Your password must have:</b></p>
                    <p> <i class="fas fa-check-circle" id="check1"></i>At least 10 characters</p>
                    <p><i class="fas fa-check-circle" id="check2"></i>Lowercase letter</p>
                    <p><i class="fas fa-check-circle" id="check3"></i>Uppercase letter</p>
                    <p><i class="fas fa-check-circle" id="check4"></i>Special characters</p>
                    <p><i class="fas fa-check-circle" id="check5"></i>Numberic value</p>
                </div>
            </div>
            <p style="font-size: 12px; color: #ff4757; text-align: left; width:100%;  margin-top:-10px; padding-left: 12.5px;">
                <?php echo isset($error_type['password']) ? $error_type['password'] : '' ?></p>

            <!-- div for password input and helper text -->
            <div class="input-fields">
                <input type="password" id="re-password" name="password2" placeholder="Confirm Password" onclick="hideHelper()">
                <span class="eye-re" onclick="rePasswordVisibility()">
                    <i id="eye3" class="fas fa-eye"></i>
                    <i id="eye4" class="fas fa-eye-slash"></i>
                </span>
            </div>
            <p style="font-size: 12px; color: #ff4757; text-align: left; width:100%;  margin-top:-10px; padding-left: 12.5px;">
                <?php echo isset($error_type['password_confirmation']) ? $error_type['password_confirmation'] : '' ?></p>

            <input type="submit" value="Update password" id="button" name="reset-password-btn">

        </form>
    </div>
    <script src="script/script.js"></script>
</body>

</html>