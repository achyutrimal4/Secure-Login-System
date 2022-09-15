<?php
session_start();

session_destroy();

header('location:signin.php');

// redirect('signin.php'); 
?>