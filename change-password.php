<?php include 'functions.php';
if (!isset($_SESSION['username'])) {
    redirect('signin.php');
} ?>

<?php
if (isset($_POST['change-password-btn'])) {

    //locating the logged-in user's data in the database using token
    if (isset($_SESSION['token'])) {

        $token = $_SESSION['token'];
        $currentPassword = trim($_POST['current-password']);
        $newpassword = trim($_POST['password1']);
        $passowrd_confirmation = trim($_POST['password2']);

        $pass1 = password_hash($newpassword, PASSWORD_BCRYPT);
        $pass2 = password_hash($passowrd_confirmation, PASSWORD_BCRYPT);

        // validating inputs and pushing errors if any
        $error_type = [
            'current_password' => '',
            'password' => '',
            'password_confirmation' => ''

        ];
        if ($currentPassword == '') {
            $error_type['current_password'] = 'This field is required';
        }
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

                $query = "SELECT * FROM user WHERE token= '$token'";
                $select_user = mysqli_query($connection, $query);
                $user_data = mysqli_fetch_assoc($select_user);
                $db_password = $user_data['password'];
                $db_username = $user_data['username'];
                $pass_decode = password_verify($currentPassword, $db_password);

                if ($pass_decode) {
                    // compare new password to the current password
                    if ($currentPassword != $newpassword) {

                        //function call to compare new password to all the previous passwords.
                        if (!compareOldPasswords($db_username, $newpassword)) {
                            echo "<p style='color:red;'>New password cannot be the same as an old password.</p>";
                        } else {
                            $updatequery = "update user set password = '$pass1' where token='$token'";
                            $query = mysqli_query($connection, $updatequery);

                            //storing previously used passwords into passwords table
                            $iquery = "INSERT INTO passwords (username, passwords) ";
                            $iquery .= "VALUES('{$db_username}', '{$db_password}')";
                            $save_password = mysqli_query($connection, $iquery);

                            if ($query) {
                                $_SESSION['reset_success_msg'] = "Password successfully changed. Login to continue .'$row_password'";
                                header('location:signin.php');
                            } else {
                                echo "<p style='color:red;'> Error occured. Please try again .</p>";
                                redirect('change-password.php');
                            }
                        }
                    } else {
                        echo "<p style='color:red;'>New password cannot be the same as current password.</p>";
                    }
                } else {
                    echo "<p style='color:red;'>Incorrect current password provided.</p>";
                }
            } else {
                $error_type['password_confirmation'] = "The two password fields do not match.";
            }
        } else {
            echo "<p style='color:red;'>Error occured. Password could not be changed.</p>";
        }
    } else {
        echo "<p style='color:red;'>User not found.</p>";
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
    <title>Change your password</title>
</head>

<body>
    <!-- alert message for expired passwords through session -->
    <div class="alert-flash">
        <?php if (isset($_SESSION['password_expired'])) {
            echo $_SESSION['password_expired'];
            unset($_SESSION['password_expired']);
        } ?>
    </div>
    <div class="container">
        <div class="header">
            <h2>Change your password.</h2>
        </div>
        <form class="form" id="form" method="POST" action="" novalidate>
            <h3>Create a strong password.</h3>

            <!-- input field for current password -->
            <div class="input-fields">
                <input type="password" id="current-password" name="current-password" placeholder="Current Password" onclick="hideHelper()">
                <span class="eye-current" onclick="currentPassVis()">
                    <i id="eye5" class="fas fa-eye"></i>
                    <i id="eye6" class="fas fa-eye-slash"></i>
                </span>
            </div>
            <p style="font-size: 12px; color: #ff4757; text-align: left; width:100%;  margin-top:-10px; padding-left: 12.5px;">
                <?php echo isset($error_type['current_password']) ? $error_type['current_password'] : '' ?></p>

            <!-- input field and helper text for New password -->
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
                <!-- password helper text -->
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

            <!-- input field for  new password confirmation -->
            <div class="input-fields">
                <input type="password" id="re-password" name="password2" placeholder="Confirm Password" onclick="hideHelper()">
                <span class="eye-re" onclick="rePasswordVisibility()">
                    <i id="eye3" class="fas fa-eye"></i>
                    <i id="eye4" class="fas fa-eye-slash"></i>
                </span>
            </div>
            <p style="font-size: 12px; color: #ff4757; text-align: left; width:100%;  margin-top:-10px; padding-left: 12.5px;">
                <?php echo isset($error_type['password_confirmation']) ? $error_type['password_confirmation'] : '' ?></p>

            <!-- button -->
            <input type="submit" value="Change password" id="button" name="change-password-btn">

        </form>
    </div>
    <script src="script/script.js"></script>
</body>

</html>