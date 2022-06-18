<?php

session_start();

if(!isSet($_SESSION["users_id"])) {
    echo "<script>window.location = 'login.php'</script>";
    exit();
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



$dbh = new Dbh();
$res = $dbh->connect()->prepare("SELECT medicine_name, medicine_type, year, month, date, marked_price, cost_price, quantity FROM medicine WHERE users_id = ?");
$res->execute(array($_SESSION["users_id"]));
echo "<br>";
$row = $res->fetchAll();
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
    <div class="dashboard">
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
        <div class="dashboard-account">
            <img src="images/user.svg" alt="user">
            <?php echo "<p>".htmlspecialchars($username['username'])."</p>"?>
        </div>
    </div>
  
    <!-- Content -->
    <div class="container">
        <div class="content">
        <header>
                <div class="left-header">
                <form class="search-container">
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
    </script>

</body>

</html>