<?php

   include "classes/dbh.classes.php";


class Signup extends Dbh {
    function __construct ($username, $email, $new_password, $confirm_password) {
        $this->username = $username;
        $this->email = $email;
        $this->new_password = $new_password;
        $this->confirm_password = password_hash($confirm_password, PASSWORD_DEFAULT);
    }

    function insert() {
        $stmt = $this->connect()->prepare("INSERT INTO users (username, users_email, users_password) VALUES(?,?,?)");
        $stmt->execute(array($this->username, $this->email, $this->confirm_password));
    }
}


if(isset($_POST["submit"])) {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];
    
    $dbh = new Dbh();
    $signup = new Signup($username, $email, $new_password, $confirm_password);
    $signup->insert();
    echo "<script>window.location = 'login.php'</script>";
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/sign_up.css">
    <title>Sign Up</title>
</head>

<body>

    <div class="container">
        <div class="left">
            <div class="image-container">
               <img src="images/login bag.svg" alt="bag">
            </div>
            <h3>Welcome!</h3>
            <p>Get access to all our services by creating your <br> new account</p>
        </div>
        <form action="#" method="post" class="right">
            <label for="signup">SIGN UP</label>
            <input type="text" name="username" id="username" placeholder="Username" required>
            <input type="email" name="email" id="email" placeholder="Email Address" required>
            <input type="password" name="new_password" id="new_password" placeholder="New Password" required>
            <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
            <div>
                <p>Already a user?</p>
                <a href="login.php">Login</a>
            </div>
            <button type="submit" name="submit" class="btn-register">REGISTER</button>
        </form>
    </div>

    <!-- Script here -->
    <script>
        const email = document.querySelector("#email"); //email input
        const submit = document.querySelector(".btn-register");
        const newPassword = document.querySelector("#new_password");
        const confirmPassword = document.querySelector("#confirm_password");

        function duplicateEmail(e) {
            let text = e.target.value;
            console.log(text);
            fetch("validation/signup_validation.php", {
                method: "POST",
                headers: { 
                    "Content-Type": "application/json" 
                },
                body: JSON.stringify({input: text})
            }).then(res => {
                return res.json();
            }).then(response => {
                console.log(response);
                if(response["result"] === "true") {
                    email.setCustomValidity("The email is already in use");
                } else {
                    email.setCustomValidity("");
                }
            }).catch(error => {
                console.log(error);
            })
        }

        function matchPassword(e) {
            console.log("New password: " + newPassword.value + "," + "confirm password: " + e.target.value);
            if(newPassword.value !== e.target.value) {
                confirmPassword.setCustomValidity("The password does not match");
            } else {
                confirmPassword.setCustomValidity("");
            }
        }

        email.addEventListener("keyup", duplicateEmail);
        confirmPassword.addEventListener("keyup", matchPassword);

    </script>
</body>

</html>