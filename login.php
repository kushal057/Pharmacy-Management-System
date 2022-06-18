<?php

if(isset($_POST["submit"])) {
    echo "<script>window.location = 'home_dashboard.php'</script>";
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/login.css">
    <title>Login</title>
</head>

<body>

    <div class="container">
        <div class="left">
            <img src="images/login bag.svg" alt="bag">
            <h3>Welcome!</h3>
            <p>Get access to all our services by signing in to <br> your existing account</p>
        </div>
        <form class="right" action="#" method="post">
            <div class="content">
                <h3>LOGIN</h3>
                <input type="email" name="email" id="email" placeholder="Email Address" required>
                <input type="password" name="password" id="password" placeholder="Password" required>
                <div>
                    <p>Not a member?</p>
                    <a href="sign_up.php">Register</a>
                </div>
                <button name="submit" class="btn-login">Login</button>
        </form>
    </div>

    <script>
        const email = document.querySelector("#email");
        const submit = document.querySelector(".btn-login");
        const password = document.querySelector("#password");

        function check() {
            let evalue = email.value;
            let pvalue = password.value;
            console.log(evalue, pvalue);
            fetch("validation/login_validation.php", {
                method: "POST",
                headers: { 
                    "Content-Type": "application/json" 
                },
                body: JSON.stringify({email: evalue, password: pvalue})
            }).then(res => {
                return res.json();
            }).then(response => {
                console.log(response);
                if(response["result"] === "false") {
                    email.setCustomValidity("Invalid email or password");
                } else {
                    email.setCustomValidity("");
                }
            }).catch(error => {
                console.log(error);
            })
        }

        password.addEventListener("input", check);
    </script>

</body>

</html>