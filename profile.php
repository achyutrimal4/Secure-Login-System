<?php 
include 'functions.php';
if (!isset($_SESSION['username'])) {
    redirect('signin.php');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
    <title>Profile</title>
    <link rel="stylesheet" type="text/css" href="styles/profile.css">    
</head>

<body> 
    <nav>
        <ul class="menu">
            <li><a href="profile.php" id="button">Profile</a></li>
            <li><a href="change-password.php" id="button">Change Password</a></li>
            <li><a href="logout.php" id="button">Logout</a></li>
        </ul>
    </nav>
    <div class="container">
        <p style="display: block;">Welcome, <?php echo $_SESSION['username'] ?>!</p> <br>
        <p><?php echo passwordExpiration($_SESSION['username'])?></p>
    </div>
    
</body>

</html>