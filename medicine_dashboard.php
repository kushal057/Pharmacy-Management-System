<?php

session_start();

if(!isSet($_SESSION["users_id"])) {
    echo "<script>window.location = 'login.php'</script>";
    exit();
}

include "classes/dbh.classes.php";

class Medicine extends Dbh
{
    private $medicine_name;
    private $medicine_type;
    private $year;
    private $month;
    private $date;
    private $cost_price;
    private $marked_price;
    private $quantity;
    private $remarks;
    private $users_id;

    function __construct($medicine_name, $medicine_type, $year, $month, $date, $cost_price, $marked_price, $quantity, $remarks, $users_id)
    {
        $this->medicine_name = $medicine_name;
        $this->medicine_type = $medicine_type;
        $this->year = $year;
        $this->month = $month;
        $this->date = $date;
        $this->cost_price = $cost_price;
        $this->marked_price = $marked_price;
        $this->quantity = $quantity;
        $this->remarks = $remarks;
        $this->users_id = $users_id;
    }

    function submit()
    {
        $sql = $this->connect()->prepare("SELECT * FROM medicine WHERE medicine_name = ? AND users_id = ?");
        $sql->execute(array($this->medicine_name, $this->users_id));
        $count = $sql->rowCount();
        $total = $sql->fetch();
        $checkQuantity = 0;
        if (!$count > 0) {
            $stmt = $this->connect()->prepare("INSERT INTO medicine(medicine_name,medicine_type,year,month,date,cost_price,
          marked_price,quantity,remarks, users_id) VALUES(?,?,?,?,?,?,?,?,?,?)");

            if (!$stmt->execute(array(
                $this->medicine_name, $this->medicine_type, $this->year, $this->month,
                $this->date, $this->cost_price, $this->marked_price, $this->quantity, $this->remarks, $this->users_id
            ))) {
                $stmt = null;
                header("location: index.php?error=stmtfailed");
                exit();
            } else {
                echo "<script>window.location = 'medicine_list.php'</script>";
            }
        } else {
            $checkQuantity += (int)$total["quantity"];
            $this->quantity = (int)$this->quantity + $checkQuantity;
           $check = $this->connect()->prepare("UPDATE medicine SET
           medicine_name = ?,
           medicine_type = ?,
           year = ?,
           month = ?,
           date = ?,
           cost_price = ?,
           marked_price = ?,
           quantity = ?,
           remarks = ?
           WHERE medicine_name = ? AND users_id = ?");
           
           if(!$check->execute(array($this->medicine_name, $this->medicine_type, $this->year, $this->month, $this->date, $this->cost_price,
           $this->marked_price, $this->quantity, $this->remarks,$this->medicine_name,$this->users_id))) {
               $check = null;
               header("location: index.php?error=stmtfailed");
               exit();
           } else {
            echo "<script>window.location = 'medicine_list.php'</script>";
           }
        }
    }
        
}


if(isset($_POST["submit"])) {
    $medicine_name = $_POST["medicine_name"];
    $medicine_type = $_POST["medicine_type"];
    $year = $_POST["year"]; 
    $month = $_POST["month"];
    $date = $_POST["date"];
    $cost_price = $_POST["cost_price"];
    $marked_price = $_POST["marked_price"];
    $quantity = $_POST["quantity"];
    $remarks = $_POST["remarks"];
    $users_id = $_SESSION["users_id"];

    $medicineObject = new Medicine( $medicine_name, $medicine_type, $year, $month,
                      $date, $cost_price, $marked_price, $quantity, $remarks, $users_id);
    $medicineObject->submit();
}

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
    <link rel="stylesheet" href="css/medicine_dashboard.css">
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
    <div class="content">
        <h3>MEDICINE DETAILS</h3>
        <form action="#" method="post">
            <div class="medicine">
                <label for="name">Medicine</label>
                <input type="text" placeholder="Medicine Name" name="medicine_name" required>
                <input type="text" placeholder="Medicine Type" name="medicine_type" required>
            </div>
            <div class="expiry">
                <label for="name">Expiry</label>
                <input class="year" type="number" placeholder="Year" name="year" required max="2100">
                <input class="month" type="number" placeholder="Month" name="month" required max="12">
                <input class="date" type="number" placeholder="Date" name="date" required max="32"> 
            </div>
            <div class="amount">
                <label for="name">Amount</label>
                <input class="cost_price" type="number" placeholder="Cost Price" name="cost_price" required maxlength="10">
                <input class="marked_price" type="number" placeholder="Marked Price" name="marked_price" required maxlength="10">
                <input class="quantity" type="number" placeholder="Quantity" name="quantity" required maxlength="4">
            </div>
            <div class="remarks">
                <label for="name">Notes</label>
                <input type="text" placeholder="Remarks" id="remarks" name="remarks" required maxlength="500">
            </div>
            <div class="button-container">
                <label for="nothing"></label>
                <button name="submit">SUBMIT</button>
            </div>
        </form>
    </div>
</body>
</html>