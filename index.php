<?php

// if(isset($_POST["btn-send"])) {
//     $to = "tiwarikushal057@gmail.com";
//     $subject = $_POST["subject"];
//     $from = $_POST["name"];
//     $headers = "From: " . $from;
//     $message = $_POST["message"];
//     mail($to,$subject,$message,$headers);
// }

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacy Management System</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
        <!-- Header -->
    <header>
            <div class="navigation">
                <div class="logo-container">
                    <p class="logo">PHARMACY</p>
                </div>
                <div class="nav-buttons">
                    <a href="login.php">Login</a>
                    <a href="sign_up.php"><button class="nav-signup">REGISTER</button></a>
                </div>
            </div>
            <div class="header-body">
                <div class="left-body">
                    <p>Manage<br>Health & Time</p>
                    <div class="header-buttons">
                        <a href="login.php"><button class="header-login">LOGIN</button></a>
                        <a href="sign_up.php"><button class="header-signup">REGISTER</button></a>
                    </div>
                </div>
                <div class="right-body">
                    <img src="images/Doctor transparent.png" alt="image">
                </div>
            </div>

    </header>

    <section class="services">
        <div class="body">
            <div class="orders">
                <img src="images/orders_icon.svg" alt="">
                <p class="title">Track Orders & Sales</p>
                <p class="body">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent lacus eros, varius.</p>
            </div>
            <div class="products">
                <img src="images/products_icon.svg" alt="">
                <p class="title">Manage Products</p>
                <p class="body">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent lacus eros, varius.</p>
            </div>
            <div class="reports">
                <img src="images/reports_icon.svg" alt="">
                <p class="title">Generate Reports</p>
                <p class="body">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent lacus eros, varius.</p>
            </div>
        </div>
    </section>

    <section class="branding">
        <div class="image-container">
            <img src="images/login vector.png" alt="">
        </div>
        <div class="text-container">
            <h3>Efficiency Made Simple</h3>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Adipisci tempore sit delectus tempora suscipit nihil!</p>
        </div>
    </section>

    <!-- <form class="footer" action="#" method="post">
        <h3>LEAVE A MESSAGE</h3>
        <div class="name">
            <p>Name</p>
            <input type="text" name="name">
        </div>
        <div class="email">
            <p>Subject</p>
            <input type="subject" name="subject">
        </div>
        <div class="message">
            <p>Message</p>
            <textarea name="message"></textarea>
        </div>
        <button name="btn-send" class="btn-send">SEND</button>
    </form> -->

    <div class="copyright">© Copyright 2022 Pharmacy Management System. All Rights Reserved</div>

    <!-- Script here -->
    <script src="js/main.js"></script>
</body>

</html> 