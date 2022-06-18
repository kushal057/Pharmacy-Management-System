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

    function submit() {;
        $conn = $this->connect()->prepare("SELECT * FROM invoice WHERE customer_name = ? AND medicine_name = ? AND users_id = ?");
        $conn->execute(array($this->customer_name, $this->medicine_name, $this->users_id));
        $invoiceCount = $conn->rowCount();

        if (!$invoiceCount > 0) {
            $delete = $this->connect()->prepare("DELETE FROM invoice WHERE customer_name = ? AND medicine_name = ? AND users_id = ?");
            $delete->execute(array($this->customer_name, $this->medicine_name, $this->users_id));

            $stmt = $this->connect()->prepare("INSERT INTO invoice(customer_name, medicine_name, price_per_unit, quantity, users_id) VALUES
            (?,?,?,?,?)");
            if (!$stmt->execute(array($this->customer_name, $this->medicine_name, $this->price_per_unit, $this->quantity, $this->users_id))) {
                $stmt = null;
                header("location: index.php?error=stmtfailed");
                exit();
            }
            else {
                echo "<script>window.location = 'invoice_list.php'</script>";
            }
        }
         else {
            $check = $this->connect()->prepare("UPDATE invoice SET
           customer_name = ?,
           medicine_name = ?,
           price_per_unit = ?,
           quantity = ?
           WHERE customer_name = ? AND medicine_name = ? AND users_id = ?");
            if (!$check->execute(array(
                $this->customer_name, $this->medicine_name, $this->price_per_unit, $this->quantity,
                $this->customer_name, $this->medicine_name, $this->users_id))) {
                $check = null;
                header("location: index.php?error=stmtfailed");
                exit();
                }
             else {
                echo "<script>window.location = 'invoice_list.php'</script>";
            }
        }
    }
}

if(isset($_POST["submit"])) {
    $customerName = $_POST["customerName"];
    $medicineName = $_POST["medicineName"];
    $pricePerUnit = $_POST["pricePerUnit"];
    $quantity = $_POST["quantity"];
    $users_id = $_SESSION["users_id"];

    $invoice = new Invoice($customerName, $medicineName, $pricePerUnit, $quantity, $users_id);
    $invoice->submit();
}

$dbh = new Dbh();
$result = $dbh->connect()->prepare("SELECT * FROM invoice WHERE invoice_id = ? AND users_id = ?");
$result->execute(array($_SESSION["invoice"], $_SESSION["users_id"]));
$row = $result->fetch();

$customerName = explode(" ",$_SESSION["customer_name"]);
$firstname = $customerName[0];
$lastname = $customerName[count($customerName) - 1];
$customer = $dbh->connect()->prepare("SELECT * FROM customer WHERE firstname = ? AND lastname = ? AND users_id = ?");
$customer->execute(array($firstname, $lastname, $_SESSION["users_id"]));
$customerCount = $customer->rowCount();
$customerData = $customer->fetch();

$medicine = $dbh->connect()->prepare("SELECT * FROM medicine WHERE medicine_name = ? AND users_id = ?");
$medicine->execute(array($_SESSION["medicine_name"], $_SESSION["users_id"]));
$medicineCount = $medicine->rowCount();
$medicineData = $medicine->fetch();
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
    <form class="dashboard" action="#" method="post">
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
        <button name="logout" class="dashboard-account">
            <img src="images/user.svg" alt="user">
            <?php echo "<p>".htmlspecialchars($username['username'])."</p>"?>
        </button>
</form>

    <!-- Content -->
    <div class="content">
        <div class="left-content">
            <h3>INVOICE</h3>
            <form action="#" method="post">
                <div class="customer-form">
                    <label for="customerName">Customer</label>
                    <input type="text" placeholder="Customer Name" id="customerName" name="customerName" required value="<?php echo htmlspecialchars($row["customer_name"])?>">
                </div>
                <div class="medicine-form">
                    <label for="name">Medicine</label>
                    <input type="text" placeholder="Medicine Name" id="medicineName" name="medicineName" required value="<?php echo htmlspecialchars($row["medicine_name"])?>">
                </div>
                <div class="amount-form">
                    <label for="name">Amount</label>
                    <div class="amount-input">
                        <input type="text" placeholder="Price per unit" id="pricePerUnit" name="pricePerUnit" required value="<?php echo htmlspecialchars($row["price_per_unit"])?>">
                        <input type="text" placeholder="Quantity" id="quantity" name="quantity" required value="<?php echo htmlspecialchars($row["quantity"])?>">
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
                    <p class="name"><?php echo $_SESSION["customer_name"] ?></p>
                    <p><?php echo $customerData["municipality"] . ", " . $customerData["district"]?></p>
                    <p><?php echo "(+977)-" . $customerData["phone"]?></p>
                    <p><?php echo $customerData["email"]?></p>
                </div>
            </div>
            <div class="medicine">
                <div class="left">
                    <p>Medicine</p>
                    <p>Type</p>
                    <p>Expiry Date</p>
                </div>
                <div class="right">
                    <p><?php echo $medicineData["medicine_name"]?></p>
                    <p><?php echo  $medicineData["medicine_type"]?></p>
                    <p><?php echo $medicineData["year"] . "-" . $medicineData["month"] . "-" . $medicineData["date"]?></p>
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

        function customerVerification(e) {
            customerInput = e.target.value;

            fetch("validation/invoice_validation.php", {
                method: "POST",
                headers: {
                    "Content-Type" :  "application/json"
                },
                body : JSON.stringify({
                    customer : customerInput
                })
            }).then(res => {
                return res.json();
            }).then(response => {
                console.log(response);
                if(response["result"] === "customerError") {
                    customerName.setCustomValidity("Customer does not exist");
                } else {
                    e.target.setCustomValidity("");
                    const customerData = Object.values(response);
                    const p = document.querySelectorAll(".customer .right p");
                    for(let i=0; i<p.length; i++) {
                        p[i].textContent = customerData[i];
                    }
                    
                 }
            })
        }

        function medicineVerification(e) {
            medicineInput = e.target.value;

            fetch("validation/invoice_validation.php", {
                method: "POST",
                headers: {
                    "Content-Type" :  "application/json"
                },
                body : JSON.stringify({
                    medicine : medicineInput
                })
            }).then(res => {
                return res.json();
            }).then(response => {
                console.log(response);
                if(response["result"] === "medicineError") {
                    medicineName.setCustomValidity("Medicine does not exist");
                } else {
                    e.target.setCustomValidity("");
                    const medicineData = Object.values(response);
                    const p = document.querySelectorAll(".medicine .right p");
                    for(let i=0; i<p.length; i++) {
                        p[i].textContent = medicineData[i];
                    }
                }
            })
        }

        medicineName.addEventListener("input", medicineVerification);
        customerName.addEventListener("input", customerVerification);

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