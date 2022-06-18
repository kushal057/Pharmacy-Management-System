<?php


if(isset($_POST["submit"])) {
    // Grabbing the input
    $username = $_POST["username"];
    $email = $_POST["email"];
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];

    //Instantiate Signup Controller class

    include "../classes/dbh.classes.php";
    include "../classes/signup.classes.php";
    include "../classes/signup-controller.classes.php";
    $signup = new SignupController($username, $email, $new_password, $confirm_password);

    // Running error handlers
    $signup->signupUser();

    //Going back to front page
    header("location: ../index.php?error=none");
} 