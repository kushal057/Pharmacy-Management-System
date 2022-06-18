<?php

session_start();

if(!isSet($_SESSION["users_id"])) {
    echo "<script>window.location = 'login.php'</script>";
    exit();
}

include "classes/dbh.classes.php";
$dbh = new Dbh();
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
    <div class="dashboard">
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
        <div class="dashboard-account">
            <img src="images/user.svg" alt="user">
            <?php echo "<p>".htmlspecialchars($username['username'])."</p>"?>
        </div>
    </div>

    <!-- Content -->
    <div class="content">
        <div class="daily-transactions left-content">
            <h3>Daily Transactions</h3>
            <div class="daily-sales">
                <img src="images/daily_sales.svg" alt="sales">
                <p><span class="daily-amount">Rs 200.5k</span><br>Sales amount</p>
            </div>
            <div class="daily-stock">
                <img src="images/daily_stock.svg" alt="stock">
                <p><span class="daily-stock">Rs 200.5k</span><br>Opening stock</p>
            </div>
            <div class="daily-profit">
                <img src="images/daily_profit.svg" alt="profit">
                <p><span class="daily-profit">20%</span><br>Percentage in profit</p>
            </div>
            <div class="daily-customer">
                <img src="images/daily_customer.svg" alt="customer">
                <p><span class="daily-customer">20</span><br>New customers</p>
            </div>
        </div>

        <div class="right-content">
            <div class="total-report">
                <div class="total-medicine">
                    <p>20</p>
                    <p>Total Medicine</p>
                </div>
                <div class="total-expired">
                    <p>20</p>
                    <p>Total expired</p>
                </div>
                <div class="total-customers">
                    <p>20</p>
                    <p>Total customers</p>
                </div>
            </div>
            <div class="quick-buttons">
                <div class="row-1">
                    <a href="#">
                        <img src="images/big_invoice.svg" alt="invoice">
                        <p>Create new invoice</p>
                    </a>
                    <a href="#">
                        <img class="medicine-vector" src="images/big_medicine.svg" alt="medicine">
                        <p>Create new medicine</p>
                    </a>
                </div>
                <div class="row-2">
                    <a href="#">
                        <img src="images/big_customer.svg" alt="customer">
                        <p>Create new customer</p>
                    </a>
                    <a href="#">
                        <img src="images/big_report.svg" alt="reports">
                        <p>View invoice list</p>
                </div>
                </a>
            </div>
        </div>
    </div>
</body>

</html>