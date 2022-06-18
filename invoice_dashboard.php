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

class Invoice extends Dbh {
    private $customer_name;
    private $medicine_name;
    private $price_per_unit;
    private $quantity;
    private $users_id;

    function __construct($customer_name, $medicine_name, $price_per_unit, $quantity, $users_id) {
        $this->customer_name = $customer_name;
        $this->medicine_name = $medicine_name;
        $this->price_per_unit = $price_per_unit;
        $this->quantity = $quantity;
        $this->users_id = $users_id;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/invoice_dashboard.css">
    <title>Invoice</title>
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
            <a href="customer_list.php" class="customers">
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
            <a href="invoice_dashboard.php" class="invoice active">
                <div class="image-container">
                    <img src="images/invoice_green.svg" alt="invoice">
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
        <div class="left-content">
            <h3>INVOICE</h3>
            <form>
                <div class="customer-form">
                    <label for="customerName">Customer</label>
                    <input type="text" placeholder="Customer Name" id="customerName" name="customerName" required>
                </div>
                <div class="medicine-form">
                    <label for="name">Medicine</label>
                    <input type="text" placeholder="Medicine Name" id="medicineName" name="medicineName" required>
                </div>
                <div class="amount-form">
                    <label for="name">Amount</label>
                    <div class="amount-input">
                        <input type="text" placeholder="Price per unit" id="pricePerUnit" name="pricePerUnit" required>
                        <input type="text" placeholder="Quantity" id="quantity" name="quantity" required>
                    </div>
                </div>
                <div class="button-container">
                    <label for="nothing"></label>
                    <button name="submit">SUBMIT</button>
                </div>
            </form>
        </div>

        <div class="right-content">
            <div class="customer">
                <div class="left">
                    <p>Name</p>
                    <p>Address</p>
                    <p>Phone</p>
                    <p>Email</p>
                </div>
                <div class="right">
                    <p class="name">John Doe</p>
                    <p>Budhanilkantha, Kathmandu</p>
                    <p>(+977)-9898989898</p>
                    <p>johndoe@gmail.com</p>
                </div>
            </div>
            <div class="medicine">
                <div class="left">
                    <p>Medicine</p>
                    <p>Type</p>
                    <p>Expiry Date</p>
                </div>
                <div class="right">
                    <p>Paracetamol</p>
                    <p>Tablet</p>
                    <p>2024-11-11</p>
                </div>
            </div>
            <div class="amount">
                <div class="left">
                    <p>Total discount</p>
                    <p>Total amount</p>
                    <p>Quantity</p>
                </div>
                <div class="right">
                    <p class="discount">Rs 2000</p>
                    <p class="price">Rs 20000</p>
                    <p class="quantity">Three</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Script here -->
    <script>
        const customerName = document.querySelector("#customerName");
        const medicineName = document.querySelector("#medicineName");
        const pricePerUnit = document.querySelector("#pricePerUnit");
        const quantity = document.querySelector("#quantity");

        function verification() {
            customerInput = customerName.value;
            medicineInput = medicineName.value;

            fetch("validation/invoice_validation.php", {
                method: "POST",
                headers: {
                    "Content-Type" :  "application/json"
                },
                body : json.stringify({
                    customer : customerInput,
                    medicine : medicineInput
                })
            }).then(res => {
                return res.json();
            }).then(response => {
                console.log(response);
                if(response["result"] === "customerError") {
                    customerName.setCustomValidity("Customer does not exist");
                } else {
                    customerName.setCustomValidity("");
                }

                if(response["result"] === "medicineError") {
                    medicineName.setCustomValidity("Medicine does not exist");
                } else {
                    customerName.setCustomValidity("");
                }

                if(response["result"] === "both") {
                    customerName.setCustomValidity("Customer does not exist");
                    medicineName.setCustomValidity("Medicine does not exist");
                } else {
                    customerName.setCustomValidity("");
                    medicineName.setCustomValidity("");
                }
            })
        }

        medicineName.addEventListener("input", verification);
    </script>


</body>

</html>