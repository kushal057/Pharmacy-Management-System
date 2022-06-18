<?php

session_start();

if(!isSet($_SESSION["users_id"])) {
    echo "<script>window.location = 'login.php'</script>";
    exit();
}

include "classes/dbh.classes.php";

class Customer extends Dbh
{
    private $firstname;
    private $lastname;
    private $year;
    private $month;
    private $date;
    private $gender;
    private $municipality;
    private $district;
    private $ward;
    private $email;
    private $phone;
    private $users_id;
    

    function __construct($firstname, $lastname, $year, $month, $date, $gender, $municipality, $district, $ward, $email, $phone, $users_id)
    {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->year = $year;
        $this->month = $month;
        $this->date = $date;
        $this->gender = $gender;
        $this->municipality = $municipality;
        $this->district = $district;
        $this->ward = $ward;
        $this->email = $email;
        $this->phone = $phone;
        $this->users_id = $users_id;
       
    }

    function submit()
    {
        $sql = $this->connect()->prepare("SELECT * FROM customer WHERE email = ? AND users_id = ?");
        $sql->execute(array($this->email, $this->users_id));
        $count = $sql->rowCount();
        if (!$count > 0) {
            $stmt = $this->connect()->prepare("INSERT INTO customer(firstname,lastname,year,month,date,gender,municipality,
            district,ward,email,phone,users_id) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)");
            $insert = array( $this->firstname, $this->lastname, $this->year, $this->month,
            $this->date, $this->gender, $this->municipality, $this->district, $this->ward, $this->email, $this->phone, $this->users_id);

            if (!$stmt->execute($insert)) {
                $stmt = null;
                header("location: index.php?error=stmtfailed");
                exit();
            } else {
                echo "<script>window.location = 'customer_list.php'</script>";
            }
        } else {
            $stmt = $this->connect()->prepare("UPDATE customer
            SET firstname = ?,
            lastname = ?,
            year = ?,
            month = ?,
            date = ?,
            gender = ?,
            municipality = ?,
            district = ?,
            ward = ?,
            email = ?,
            phone = ?,
            WHERE
            email = ? AND users_id = ?");

            if (!$stmt->execute(array(
                $this->firstname, $this->lastname, $this->year, $this->month,  $this->date,
                $this->gender, $this->municipality, $this->district, $this->ward, $this->email, $this->phone, $this->email,  $this->users_id
            ))) {
                $stmt = null;
                header("location: index.php?error=stmtfailed");
                exit();
            } else {
                echo "<script>window.location = 'customer_list.php'</script>";
            }
        }
    }
}


if(isset($_POST["submit"])) {
    $firstname = $_POST["firstname"];
    $lastname = $_POST["lastname"];
    $year = $_POST["year"]; 
    $month = $_POST["month"];
    $date = $_POST["date"];
    $gender = $_POST["gender"];
    $municipality = $_POST["municipality"];
    $district = $_POST["district"];
    $ward = $_POST["ward"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $users_id = $_SESSION["users_id"];

    $customerObject = new Customer( $firstname, $lastname, $year, $month,
                      $date, $gender, $municipality, $district, $ward, $email, $phone, $users_id);
    $customerObject->submit();
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
    <link rel="stylesheet" href="css/customer_dashboard.css">
    <title>Customer</title>
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
        <div class="dashboard-account">
            <img src="images/user.svg" alt="user">
            <?php echo "<p>".htmlspecialchars($username['username'])."</p>"?>
        </div>
    </div>

    <!-- Content -->
    <div class="content">
        <h3>CUSTOMER DETAILS</h3>
        <form action="#" method="post">
        <div class="name">
                <label for="name">Name</label>
                <input type="text" placeholder="First name" name="firstname" required>
                <input type="lastname" placeholder="Last name" name="lastname" required>
            </div>
            <div class="birth-date">
                <label for="name">Birthdate</label>
                <input class="year" type="number" placeholder="Year" name="year" required max="2100">
                <input class="month" type="number" placeholder="Month" name="month" required max="12">
                <input class="date" type="number" placeholder="Date" name="date" required max="32"> 
                <select id="gender" name="gender">
                    <option value="male" name="gender">Male</option>
                    <option value="female" name="gender">Female</option>
                </select>
            </div>
            <div class="address">
                <label for="name">Address</label>
                <input type="text" placeholder="Municipality" name="municipality" required>
                <input type="text" placeholder="District" name="district" required>
                <input type="number" class="ward" placeholder="Ward" name="ward" required maxlength="2">
            </div>
            <div class="contact">
                <label for="name">Contact</label>
                <input type="email" placeholder="Email Address" name="email" required>
                <input type="text" placeholder="Phone Number" name="phone" required maxlength="10">
            </div>
            <div class="button-container">
                <label for="nothing"></label>
                <button class="submit" name="submit">SUBMIT</button>
            </div>
        </form>
    </div>
</body>
</html>