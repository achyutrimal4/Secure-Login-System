<?php include 'database.php';

session_start();

// database connection function
function db_connection($result)
{
    global $connection;
    if (!$result) {
        die('Query Failed' . mysqli_error($connection));
    }
}

//query confirm
function confirm_Query($result)
{
    global $connection;
    if (!$result) {
        die('Query Failed' . mysqli_error($connection));
    }
}

// redirect function
function redirect($string)
{
    return header("Location:" . $string);
    exit;
}

// function to prevent sql injection
function escape_string($string)
{
    global $connection;
    return mysqli_real_escape_string($connection, trim($string));
}


//function to check if the username already exists
function username_exists($username)
{
    global $connection;
    $query = "SELECT username FROM user WHERE username = '$username'";
    $result = mysqli_query($connection, $query);
    confirm_Query($result);
    $row = mysqli_num_rows($result);

    if ($row > 0) {
        return true;
    } else {
        return false;
    }
}

//function to check if the email already exists
function email_exists($email)
{
    global $connection;
    $query = "SELECT email FROM user WHERE email = '$email'";
    $result = mysqli_query($connection, $query);
    confirm_Query($result);
    $row = mysqli_num_rows($result);

    if ($row > 0) {
        return true;
    } else {
        return false;
    }
}

// function to register user
function user_registration($fullname, $username, $email, $password)
{
    global $connection;
    $fullname = mysqli_real_escape_string($connection, $fullname);
    $username = mysqli_real_escape_string($connection, $username);
    $email = mysqli_real_escape_string($connection, $email);
    $password = mysqli_real_escape_string($connection, $password);
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    $token = bin2hex(random_bytes(15));
    date_default_timezone_set("Asia/Kathmandu");
    $date = date("d-m-y");
    $verification_key = sha1(time() . $email);
    $_SESSION['email'] = $email;
    

    $query = "INSERT INTO user (fullname, username, email, password, verification_key, is_verified, date, otp, token) ";
    $query .= "VALUES('{$fullname}','{$username}', '{$email}',  
                '{$password_hash}',  '{$verification_key}', '0', '{$date}', '0', '{$token}')";

    $user_register = mysqli_query($connection, $query);

    //query to store password into passwords table
    $iquery = "INSERT INTO passwords (username, passwords) ";
    $iquery .= "VALUES('{$username}', '{$password_hash}')";
    $save_password = mysqli_query($connection, $iquery);

    //send mail with email verification link
    if ($user_register) {
        $subject = "Email Verification";
        $from = "acs.achyut@gmail.com";

        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

        $message = "
        <html>
            <head>
                <title>
        
                </title>
            </head>
            <body>
                <p> Thank you for registration</p>
                <p>Please click below to activate your account.</p>
                <a href='http://localhost/ACS/verify_email.php?verification_key=$verification_key&email=$email'>
                    Click Here</a>
                    <p>
                    CONFIDENTIALITY NOTICE: This transmittal is a confidential communication or may otherwise be 
                    privileged. If you are not the intended recipient, you are hereby notified that you have received 
                    this transmittal in error and that any review, dissemination, distribution or copying of this
                    transmittal is strictly prohibited. If you have received this communication in error, 
                    please notify this office, and immediately delete this message and all its attachments, if any. </p>
            </body>
        </html>";

        mail($email, $subject, $message, $headers);
        redirect('notice.php');
    }
}

// login function
function user_login($username, $password)
{
    global $connection;
    $username = mysqli_real_escape_string($connection, $username);
    $password = mysqli_real_escape_string($connection, $password);

    $query = "SELECT * FROM user WHERE username= '$username' AND is_verified = 1";
    $select_user = mysqli_query($connection, $query);
    // check if username exists
    $db_username = mysqli_num_rows($select_user);

    if ($db_username) {
        $user_data = mysqli_fetch_assoc($select_user);
        $db_password = $user_data['password'];
        $db_email = $user_data['email'];
        $db_username = $user_data['username'];
        $db_token = $user_data['token'];

        //compare passwords
        $pass_decode = password_verify($password, $db_password);

        if ($pass_decode) {
            $_SESSION['email'] = $db_email;
            $_SESSION['username'] = $db_username;
            $_SESSION['token'] = $db_token;

            //function to send otp
            sendOtpEmail($db_email);
            redirect("verify_login.php");
        } else {
            echo "<p style='color:#f53b3b;'>Incorrect username or password.</p>";
        }
    } else {
        echo "<p style='color:#f53b3b;'>Incorrect username or password.</p>";
    }
}

// get email-verification
function getVerificationKey($email, $verification_key)
{
    global $connection;
    $email = mysqli_real_escape_string($connection, $email);
    $verification_key = mysqli_real_escape_string($connection, $verification_key);
    $query = "UPDATE user SET verification_key = '0', is_verified = 1 WHERE email = '{$email}' AND verification_key = '{$verification_key}'";
    $result = mysqli_query($connection, $query);
    confirm_Query($result);
    if (!$result) {
        return false;
    }
    return true;
}

//function to check if the email is verified
function isEmailVerified($email)
{
    global $connection;
    $email = mysqli_real_escape_string($connection, trim($email));
    $query = "SELECT * FROM user WHERE email = '{$email}' AND is_verified = 0";
    $select_user = mysqli_query($connection, $query);
    confirm_Query($select_user);
    $row = mysqli_num_rows($select_user);
    if ($row > 0) {
        return true;
    } else {
        return false;
    }
}

// send email with otp
function sendOtpEmail($email)
{
    $otp = rand(100000, 999999);
    $subject = "Two Factor Authentication";
    $from = 'noreply@ACS.com';

    // To send HTML mail, the Content-type header must be set
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

    // Create email headers
    $headers .= 'From: ' . $from . "\r\n" .
        'Reply-To: ' . $from . "\r\n" .
        'X-Mailer: PHP/' . phpversion();
    $message = "<p>The verification code is</p>
            <p><b>$otp</b></p>
            <p>
                    CONFIDENTIALITY NOTICE: This transmittal is a confidential communication or may otherwise be 
                    privileged. If you are not the intended recipient, you are hereby notified that you have received 
                    this transmittal in error and that any review, dissemination, distribution or copying of this
                    transmittal is strictly prohibited. If you have received this communication in error, 
                    please notify this office, and immediately delete this message and all its attachments, if any. </p>";

    mail($email, $subject, $message, $headers);

    global $connection;
    $email = mysqli_real_escape_string($connection, $email);
    $otp = mysqli_real_escape_string($connection, $otp);
    $query = "UPDATE user SET otp = $otp WHERE email = '{$email}'";
    mysqli_query($connection, $query);
}

//check if the entered otp is correct
function otpVerification($email, $otp)
{
    global $connection;
    $email = mysqli_real_escape_string($connection, $email);
    $otp = mysqli_real_escape_string($connection, $otp);
    $query = "SELECT otp FROM user WHERE email= '{$email}'";
    $select_user = mysqli_query($connection, $query);
    while ($row = mysqli_fetch_array($select_user)) {
        $db_otp = $row['otp'];
        if ($otp == $db_otp) {
            return true;
        } else {
            return false;
        }
    }
}
//update otp to 0 after login and expire it
function updateOtp($email, $otp)
{
    global $connection;
    $email = mysqli_real_escape_string($connection, $email);
    $otp = mysqli_real_escape_string($connection, $otp);
    $query = "UPDATE user SET otp = '0'WHERE email = '{$email}' AND otp = '{$otp}'";
    $result = mysqli_query($connection, $query);
    confirm_Query($result);
    if (!$result) {
        return false;
    }
    return true;
};

//compare new password to old passwords
function compareOldPasswords($username, $password)
{
    global $connection;
    $query = "SELECT * FROM passwords where username = '$username'";
    $user_data = mysqli_query($connection, $query);
    confirm_Query($user_data);
    while ($row = mysqli_fetch_array($user_data)) {
        $old_password = $row['passwords'];
        if (password_verify($password, $old_password)) {
            return false;
        }
    }
    return true;
}

//function to calculate date difference (Date of registration and today)
function dateDiff($date1, $date2)
{
    $difference = strtotime($date2) - strtotime($date1);
    return abs(round($difference / 86400));
}

//check if the password has expired
function passwordExpiration($username)
{
    global $connection;
    date_default_timezone_set("Asia/Kathmandu");
    $date = date("d-m-y");

    $username = mysqli_real_escape_string($connection, $username);
    $query = "SELECT date FROM user where username='$username'";
    $result = mysqli_query($connection, $query);

    while ($user_data = mysqli_fetch_array($result)) {
        $db_date = $user_data['date'];
        $days_count = 30 - dateDiff($db_date, $date);
        if ($days_count > 1 && $days_count <= 30) {
            echo "Your password will expire after " . $days_count . "day/s";
        } else {
            $_SESSION['password_expired'] = "Your password has expired. Please change your password to continue";
            header('location:change-password.php');
        }
    }
}


