<?php

session_start();

if(!isSet($_SESSION["users_id"])) {
    echo "<script>window.location = 'login.php'</script>";
    exit();
}

if(isSet($_POST["logout"])) {
    session_unset();
    echo "<script>window.location = 'login.php'</script>";
}

include "classes/dbh.classes.php";

if(isset($_POST["btn-edit"])) {
    echo "<script>window.location.href = 'customer_edit.php'</script>";
    $_SESSION["edit"] = $_POST["edit"];
}

if(isset($_POST["btn-delete"])) {
    $dbh = new Dbh();
    $delete = $dbh->connect()->prepare("DELETE FROM customer WHERE email = ? AND users_id = ?");
    $search =  $_POST["delete"];
    $delete->execute(array($search, $_SESSION["users_id"]));
    echo "<script>window.location.href = '#'</script>";
}

$dbh = new Dbh();
$result = $dbh->connect()->prepare("SELECT firstname, lastname, phone, email FROM customer WHERE users_id = ?");
$result->execute(array($_SESSION["users_id"]));
$row = $result->fetchAll();
$result = $dbh->connect()->prepare("SELECT username FROM users WHERE users_id = ?");
$result->execute(array($_SESSION["users_id"]));
$username = $result->fetch();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/customer_list.css">
    <title>Customer</title>
</head>

<body>
    <!-- Dashboard -->
    <form class="dashboard" action="#" method="post">
        <div class="dashboard-menus">
            <h3>DASHBOARD</h3>
            <a href="home_dashboard.php" class="home">
                <div class="image-container">
                    <img src="images/home.svg" alt="home">
                </div>
                <p>Home</p>
            </a>
            <a href="customer_list.php" class="customer active">
                <div class="image-container">
                    <img src="images/customer_green.svg" alt="customer">
                </div>
                <p>Customer</p>
            </a>
            <a href="medicine_list.php" class="medicine">
                <div class="image-container">
                    <img src="images/tablet.svg" alt="medicine">
                </div>
                <p>Medicine</p>
            </a>
            <a href="invoice_list.php" class="invoice">
                <div class="image-container">
                    <img src="images/invoice.svg" alt="invoice">
                </div>
                <p>Invoice</p>
            </a>
        </div>
        <button name="logout" class="dashboard-account">
            <img src="images/user.svg" alt="user">
            <?php echo "<p>".htmlspecialchars($username['username'])."</p>"?>
        </button>
</form>
  
    <!-- Content -->
    <div class="container">
        <div class="content">
        <header>
                <div class="left-header">
                <form class="search-container">
                        <input type="text" name="search" placeholder="Customer name" class="searchbar">
                        <input type="submit" name="btn-search" class="btn-search" value="Search">
                </form>
                <!-- <ul class="suggestions">
                    <li>This</li>
                    <li>This</li>
                    <li>This</li>
                    <li>This</li>
                </ul> -->
                </div>
                <div class="right-header">
                   
                    <a href="customer_dashboard.php">Add New Customer</a>
                </div>
            </header>
            <div class="customer-list">
                <div id="title">
                    <div class="text">
                        <p>First name</p>
                        <p>Last Name</p>
                        <p>Phone(+977)</p>
                        <p>Email address</p>
                    </div>
                </div>
                <?php 
                    forEach($row as $i) {
                        echo "<div class='row'>";
                        echo "<div class='text'>";
                            echo "<p class='firstname'>".htmlspecialchars($i['firstname'])."</p>";
                            echo "<p class='lastname'>".htmlspecialchars($i['lastname'])."</p>";
                            echo "<p class='phone'>".htmlspecialchars($i['phone'])."</p>";
                            echo "<p class='email'>".htmlspecialchars($i['email'])."</p>";
                        echo "</div>";
                        echo "<div class='buttons'>";
                        echo "<form action='#' method='post'>";
                            echo "<button name='btn-edit'><img src='images/edit icon.svg' alt='edit' class='btn-edit'></button>";
                            echo "<input type='hidden' value=".$i['email']." name='edit'>";
                            echo "</form>";
                            echo "<form action='#' method='post'>";
                            echo "<input type='hidden' value=".$i['email']." name='delete'>";
                            echo "<button name='btn-delete'><img src='images/delete icon.svg' alt='edit' class='btn-delete'></button>";
                            echo "</form>";
                        echo "</div>";
                       echo "</div>"; 
                    }
                    ?>
        </div>
    </div>

    <script>
        const searchbar = document.querySelector(".searchbar");
        const suggestions = document.querySelector(".suggestions");
        const list = document.querySelectorAll("li");
        function retrieve(e) {
           let search = e.target.value;
           console.log(search);
           let xmlhttp = new XMLHttpRequest();
           xmlhttp.addEventListener("readyStateChange", () => {
               if(this.readyState == 4 && this.status == 200) {
                   let myObj = JSON.parse(this.responseText);
                   console.log(myObj);
               }
           });
           xmlhttp.open("POST","customer_list.php?q=" + search, true);
           xmlhttp.send();
        }
        searchbar.addEventListener("keyup",retrieve)

        // Logout
        const accountDiv = document.querySelector(".dashboard-account");
        const accountImage = document.querySelector(".dashboard-account img");
        const accountName = document.querySelector(".dashboard-account p");
        const originalName = accountName.textContent;

        function hovered() {
            accountImage.src = "images/logout_icon.svg";
            accountName.textContent = "logout";
            accountName.style.color = "#FF2E2E"
        }

        function unhovered() {
            accountImage.src = "images/user.svg";
            accountName.textContent = originalName;
            accountName.style.color = "var(--text-dark)";
        }

        accountDiv.addEventListener("mouseover", hovered);
        accountDiv.addEventListener("mouseout", unhovered);
    </script>

</body>

</html>