<?php include 'functions.php';

if (isset($_POST['send-mail-btn'])) {
    // query using input email
    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $db_userdata = " select * from user where email = '$email'";
    $query = mysqli_query($connection, $db_userdata);

    // checking if provided email exists in the database
    $emailcount = mysqli_num_rows($query);

    if ($emailcount) {
        $userdata = mysqli_fetch_array($query);
        $username = $userdata['username'];
        $token = $userdata['token'];

        $subject = "Password Recovery";
        $from = "acs.achyut@gmail.com";

        //send mail with password reset link
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

        $message = "
            <html>
                <head>
                    <title>
            
                    </title>
                </head>
                <body>
                    Hello, $username. Click the link below to reset your password. 
                    <br>
                    <p><i>If you didnot request this email please ignore this email and change your password.</i></p><br>

                    <a href='http://localhost/ACS/reset-password.php?token=$token'>Click Here to reset the password.</a>
                </body>
            </html>";

        mail($email, $subject, $message, $headers);
        $_SESSION['link_sent_msg'] = "Password reset link sent to your email. Follow that link to continue";
        header('location:signin.php');
    } else {
        echo "<p style='color:red;'> The provided email is not registered.</p>";
    }
} ?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="styles/style.css">
    <title>Enter your email.</title>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>Reset Password</h2>
        </div>
        <form class="form" id="form" method="post" action="" novalidate>

            <p>
            </p>
            <h3>Please enter your registered email to change the password.</h3>

            <p>An email with a link to change the password will be sent to your email address.</p>
            <!-- input field for email -->
            <div class="input-fields ">
                <input type="text" id="email" name="email" placeholder="Email" onclick="hideHelper()">
            </div>

            <!-- button -->
            <input type="submit" value="send email" id="button" name="send-mail-btn">

        </form>
    </div>
    <!-- <script src="signup.js"></script> -->
</body>

</html>