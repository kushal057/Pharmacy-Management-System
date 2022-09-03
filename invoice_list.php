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
    echo "<script>window.location.href = 'invoice_edit.php'</script>";
    $_SESSION["invoice"] = $_POST["invoice"];
}

if(isset($_POST["btn-delete"])) {
    $dbh = new Dbh();
    $delete = $dbh->connect()->prepare("DELETE FROM invoice WHERE invoice_id = ? AND users_id = ?");
    $search =  $_POST["delete"];
    $delete->execute(array($search,$_SESSION["users_id"]));
    echo "<script>window.location.href = '#'</script>";
}



$row;
if(isset($_POST["btn-search"])) {
        $dbh = new Dbh();
        $search = $dbh->connect()->prepare("SELECT * FROM invoice WHERE users_id = ? AND customer_name LIKE ?");
        $search->execute(array($_SESSION["users_id"], $_POST["search"] . "%"));
        $row = $search->fetchAll();
} else {
    $dbh = new Dbh();
    $result = $dbh->connect()->prepare("SELECT * FROM invoice WHERE users_id = ?");
    $result->execute(array($_SESSION["users_id"]));
    $row = $result->fetchAll();
}

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
    <link rel="stylesheet" href="css/invoice_list.css">
    <title>Invoice</title>
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
            <a href="customer_list.php" class="customer">
                <div class="image-container">
                    <img src="images/customer.svg" alt="customer">
                </div>
                <p>Customer</p>
            </a>
            <a href="medicine_list.php" class="medicine">
                <div class="image-container">
                    <img src="images/tablet.svg" alt="medicine">
                </div>
                <p>Medicine</p>
            </a>
            <a href="invoice_list.php" class="invoice active">
                <div class="image-container">
                    <img src="images/invoice_green.svg" alt="invoice">
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
                <form class="search-container" action="#" method="post">
                        <input type="text" name="search" placeholder="Customer Name" class="searchbar">
                        <input type="submit" name="btn-search" class="btn-search" value="Search">
                </form>
                <ul class="suggestions"></ul>
                </div>
                <div class="right-header">
                   
                    <a href="invoice_dashboard.php">Add New Invoice</a>
                </div>
            </header>
            <div class="invoice-list">
                <div id="title">
                    <div class="text">
                        <p>Customer Name</p>
                        <p>Medicine Name</p>
                        <p>Price Per Unit</p>
                        <p>Quantity</p>
                        <p>Date Added</p>
                        <p>Invoice ID</p>
                    </div>
                </div>
                <?php 
                if(sizeof($row) <= 0) {
                    echo "<div class='row'>";
                       echo "<div class='text'>";
                          echo "<p>No items found</p>";
                       echo "</div>";   
                    echo "</div>";
                }
                forEach($row as $i) {
                    $_SESSION["customer_name"] = $i['customer_name'];
                    $_SESSION["medicine_name"] = $i["medicine_name"];
                    $yearMonthDay = explode(" ", $i['date_added']);
                    echo "<div class='row'>";
                    echo "<div class='text'>";
                        echo "<p class='customer_name'>".htmlspecialchars($i['customer_name'])."</p>";
                        echo "<p class='medicine_name'>".htmlspecialchars($i['medicine_name'])."</p>";
                        echo "<p class='price_per_unit'>Rs ".htmlspecialchars($i['price_per_unit'])."</p>";
                        echo "<p class='quantity'>Rs ".htmlspecialchars($i['quantity'])."</p>";
                        
                        echo "<p class='date_added'>".$yearMonthDay[0]."</p>";
                        echo "<p class='invoice_id'>".htmlspecialchars($i['invoice_id'])."</p>";
                    echo "</div>";
                    echo "<div class='buttons'>";
                    echo "<form action='#' method='post'>";
                            echo "<button name='btn-edit'><img src='images/edit icon.svg' alt='edit' class='btn-edit'></button>";
                            echo "<input type='hidden' value=".$i['invoice_id']." name='invoice'>";
                            echo "</form>";
                        echo "<form action='#' method='post'>";
                        echo "<input type='hidden' value=".$i['invoice_id']." name='delete'>";
                        echo "<button name='btn-delete'><img src='images/delete icon.svg' title='Delete' alt='edit' class='btn-delete'></button>";
                        echo "</form>";
                    echo "</div>";
                   echo "</div>"; 
                }?>
        </div>
    </div>

    <script>
        const searchbar = document.querySelector(".searchbar");
        const suggestions = document.querySelector(".suggestions");
        
        function retrieve(e) {
            suggestions.style.display = "flex"
           let search = e.target.value;
           fetch("autocomplete.php", {
               method: "POST",
               headers: {
                   "Content-Type": "application/json"
               },
               body: JSON.stringify({
                   input: search
               }) 
           }).then(res=> {
               return res.json();
           }).then(data => {
               console.log(data);
                for(let value of data) {
                    while(suggestions.firstChild) {
                        suggestions.removeChild(suggestions.lastChild);
                    }
                    const list = document.createElement("li");
                    list.textContent = value["medicine_name"];
                    suggestions.appendChild(list);
                    list.addEventListener("click", (e)=>{
                        searchbar.value = value["medicine_name"];
                        suggestions.style.display = "none";
                    })
                }
           }).catch(error => console.log("error" + error));
        }
        searchbar.addEventListener("keyup",retrieve)
        searchbar.addEventListener("focusout",()=>{
            suggestions.style.display = "none";
            e.target.value = "";
        })

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