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
    echo "<script>window.location.href = 'medicine_edit.php'</script>";
    $_SESSION["old"] = $_POST["old"];
}

if(isset($_POST["btn-delete"])) {
    $dbh = new Dbh();
    $delete = $dbh->connect()->prepare("DELETE FROM medicine WHERE medicine_name = ? AND users_id = ?");
    $search =  $_POST["delete"];
    $delete->execute(array($search,$_SESSION["users_id"]));
    echo "<script>window.location.href = '#'</script>";
}

$row;
if(isset($_POST["btn-search"])) {
        $dbh = new Dbh();
        $search = $dbh->connect()->prepare("SELECT * FROM medicine WHERE users_id = ? AND medicine_name LIKE ?");
        $search->execute(array($_SESSION["users_id"], $_POST["search"] . "%"));
        $row = $search->fetchAll();
} else {
    $dbh = new Dbh();
    $res = $dbh->connect()->prepare("SELECT medicine_name, medicine_type, year, month, date, marked_price, cost_price, quantity FROM medicine WHERE users_id = ?");
    $res->execute(array($_SESSION["users_id"]));
    $row = $res->fetchAll();
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
    <link rel="stylesheet" href="css/medicine_list.css">
    <title>Medicine</title>
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
            <a href="medicine_list.php" class="medicine active">
                <div class="image-container">
                    <img src="images/medicine_green.svg" alt="medicine">
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
                <form class="search-container" action="#" method="post">
                        <input type="text" name="search" placeholder="Medicine name" class="searchbar">
                        <input type="submit" name="btn-search" class="btn-search" value="Search">
                </form>
                <ul class="suggestions"></ul>
                </div>
                <div class="right-header">
                   
                    <a href="medicine_dashboard.php">Add New Medicine</a>
                </div>
            </header>
            <div class="medicine-list">
                <div id="title">
                    <div class="text">
                        <p>Medicine Name</p>
                        <p>Type</p>
                        <p>Marked Price</p>
                        <p>Cost Price</p>
                        <p>Expiry Date</p>
                        <p>Quantity</p>
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
                    echo "<div class='row'>";
                    echo "<div class='text'>";
                        echo "<p class='medicine_name'>".htmlspecialchars($i['medicine_name'])."</p>";
                        echo "<p class='type'>".htmlspecialchars($i['medicine_type'])."</p>";
                        echo "<p class='marked_price'>Rs ".htmlspecialchars($i['marked_price'])."</p>";
                        echo "<p class='cost_price'>Rs ".htmlspecialchars($i['cost_price'])."</p>";
                        echo "<p class='expiry'>".htmlspecialchars($i['year'])."-".htmlspecialchars($i['month'])."-".htmlspecialchars($i['date'])."</p>";
                        echo "<p class='quantity'>".htmlspecialchars($i['quantity'])."</p>";
                    echo "</div>";
                    echo "<div class='buttons'>";
                    echo "<form action='#' method='post'>";
                            echo "<button name='btn-edit'><img src='images/edit icon.svg' alt='edit' class='btn-edit'></button>";
                            echo "<input type='hidden' value=".$i['medicine_name']." name='old'>";
                            echo "</form>";
                        echo "<form action='#' method='post'>";
                        echo "<input type='hidden' value=".$i['medicine_name']." name='delete'>";
                        echo "<button name='btn-delete'><img src='images/delete icon.svg' title='Delete' alt='edit' class='btn-delete'></button>";
                        echo "</form>";
                    echo "</div>";
                   echo "</div>"; 
                }?>
        </div>
    </div>

</body>

</html>