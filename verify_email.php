<?php include "functions.php" ?>

<?php
if (isset($_GET['verification_key']) && isset($_GET['email'])) {
    $verification_key = $_GET['verification_key'];
    $email = $_GET['email'];
    if (getVerificationKey($email, $verification_key)) {
?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
            <link rel="stylesheet" type="text/css" href="styles/verify_email.css">
            <title>Verify your email</title>
        </head>

        <body>
            <div class="container ">
                <h3> Congratulations! Your account has been successfully activated. You can now login to your account.</h3>
                <button id="button" onclick="location.href = ' signin.php'">login</button>
            </div>
        </body>
        </html>
<?php
    } else {
        echo "<p style='color:#f53b3b;'>Sorry your account could not be activated. Try again.</p>";
    }
}
?>