<?php

class Login extends Dbh{

    protected function getUser($username, $password) {
        $stmt = $this->connect()->prepare("SELECT users_password FROM login WHERE username = ? OR users_email = ?;");

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        if(!$stmt->execute(array($username, $hashedPassword))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }

        if($stmt->rowCount() == 0) {
            $stmt = null;
            header("location: ../index.php?error=usernotofound");
            exit();
        }

        $passwordHashed = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $checkPassword = password_verify($password, $passwordHashed[0]["users_password"]);
        $stmt = null;

        if($checkPassword == false) {
            $stmt = null;
            header("location: ../index.php?error=wrongpassword");
            exit();
        } else if($checkPassword = true) {
            $stmt = $this->connect()->prepare("SELECT users_password FROM login WHERE username = ? OR users_email = ? AND users_password = ?;");

            if(!$stmt->execute(array($username, $username, $password))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }

            if($stmt->rowCount == 0) {
                $stmt = null;
                header("location: ../index.php?error=usernotfound");
                exit();
            }

            $user = $stmt->fetchAll(PDO::FETCH_ASSOC);

            session_start();
            $_SESSION["userid"] = $user[0]["users_id"];
            $_SESSION["username"] = $user[0]["username"];
        }
    }
}
