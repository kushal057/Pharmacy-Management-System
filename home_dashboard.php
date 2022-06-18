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
$dbh = new Dbh();

// Sales Amount
$sales = $dbh->connect()->prepare("SELECT price_per_unit, quantity FROM invoice WHERE users_id = ?");
$sales->execute(array($_SESSION["users_id"]));
$salesData = $sales->fetchAll();
$dailySales = 0;
if($sales->rowCount() > 0) {
    forEach($salesData as $unit) {
        $dailySales += (int) $unit["quantity"] * (int) $unit["price_per_unit"];
    }
    if($dailySales > 1000) {
        $dailySales = $dailySales / 1000;
        $dailySales = $dailySales . "k";
    }
}

// Total Stock
$stock = $dbh->connect()->prepare("SELECT marked_price, quantity FROM medicine WHERE users_id = ?");
$stock->execute(array($_SESSION["users_id"]));
$stockData = $stock->fetchAll();
$dailyStock = 0;
if($stock->rowCount() > 0) {
    forEach($stockData as $unit) {
        $dailyStock += (int) $unit["quantity"] * (int) $unit["marked_price"];
    }
    if($dailyStock > 1000) {
        $dailyStock = $dailyStock / 1000;
        $dailyStock = $dailyStock . "k";
    }
}

//Profit Amount
$sp = $dbh->connect()->prepare("SELECT medicine_name, price_per_unit, quantity FROM invoice WHERE users_id = ?");
$sp->execute(array($_SESSION["users_id"]));
$spData = $sp->fetchAll();
$spAmount = 0;
$salesQuantity = 0;
$cpAmount = 0;
forEach($spData as $unit) {
    $spAmount = (int) $unit["price_per_unit"] * (int) $unit["quantity"];
    $medicineName = $unit["medicine_name"];

    $cp = $dbh->connect()->prepare("SELECT cost_price FROM medicine WHERE medicine_name = ? AND users_id = ?");
    $cp->execute(array($medicineName,$_SESSION["users_id"]));
    $cpData = $cp->fetch();
    $cpAmount += $cpData["cost_price"] * (int) $unit["quantity"];
}

$profit = 0;
if($sp->rowCount() > 0) {
    $profit = $spAmount - $cpAmount;
    if($profit > 1000) {
        $profit = $profit / 1000;
        $profit = $profit . "k";
    }
}

// Total medicine
$totalMedicine = $dbh->connect()->prepare("SELECT * FROM medicine WHERE users_id = ?");
$totalMedicine->execute(array($_SESSION["users_id"]));
$totalMedicine = $totalMedicine->rowCount();

// Total customers
$totalCustomers = $dbh->connect()->prepare("SELECT * FROM customer WHERE users_id = ?");
$totalCustomers->execute(array($_SESSION["users_id"]));
$totalCustomers = $totalCustomers->rowCount();

// Total expired
$expiry = $dbh->connect()->prepare("SELECT year, month, date FROM medicine WHERE users_id = ?");
$expiry->execute(array($_SESSION["users_id"]));
$expiryData = $expiry->fetchAll();
$totalExpired = 0;
$flag = 0;
forEach($expiryData as $one) {
    $firstDate = strtotime($one["year"] . "-" . $one["month"] . "-" . $one["date"]);
    $secondDate = date("Y-m-d", strtotime("now"));

    if($firstDate == $secondDate) {
        $flag++;
    }
}
if($flag > 0) {
    $totalExpired = $flag;
}

// Display username
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
    <link rel="stylesheet" href="css/home_dashboard.css">
    <title>Home</title>
</head>

<body>
    <!-- Dashboard -->
    <form class="dashboard" action="#" method="post">
        <div class="dashboard-menus">
            <h3>DASHBOARD</h3>
            <a href="home_dashboard.php" class="home active">
                <div class="image-container">
                    <img src="images/home_green.svg" alt="home">
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
    <div class="content">
        <div class="daily-transactions left-content">
            <h3>Financial Report</h3>
            <div class="daily-sales">
                <img src="images/daily_sales.svg" alt="sales">
                <p><span class="daily-amount">Rs <?php echo $dailySales?></span><br>Sales Amount</p>
            </div>
            <div class="daily-stock">
                <img src="images/daily_stock.svg" alt="stock">
                <p><span class="daily-stock">Rs <?php echo $dailyStock?></span><br>Opening stock</p>
            </div>
            <div class="daily-profit">
                <img src="images/daily_profit.svg" alt="profit">
                <p><span class="daily-profit">Rs <?php echo $profit?></span><br>Total Stock</p>
            </div>
        </div>

        <div class="right-content">
            <div class="total-report">
                <div class="total-medicine">
                    <p><?php echo $totalMedicine?></p>
                    <p>Total Medicine</p>
                </div>
                <div class="total-expired">
                    <p><?php echo $totalExpired?></p>
                    <p>Total expired</p>
                </div>
                <div class="total-customers">
                    <p><?php echo $totalCustomers?></p>
                    <p>Total customers</p>
                </div>
            </div>
            <div class="quick-buttons">
                <div class="row-1">
                    <a href="invoice_dashboard.php">
                        <img src="images/big_invoice.svg" alt="invoice">
                        <p>Create new invoice</p>
                    </a>
                    <a href="medicine_dashboard.php">
                        <img class="medicine-vector" src="images/big_medicine.svg" alt="medicine">
                        <p>Add new medicine</p>
                    </a>
                </div>
                <div class="row-2">
                    <a href="customer_dashboard.php">
                        <img src="images/big_customer.svg" alt="customer">
                        <p>Add new customer</p>
                    </a>
                    <a href="invoice_list.php">
                        <img src="images/big_report.svg" alt="reports">
                        <p>View invoice list</p>
                </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Script here -->
    <script>
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