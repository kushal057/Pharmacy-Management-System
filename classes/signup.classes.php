<?php

class Signup extends Dbh{

    protected function setUser($username, $password, $email) {
        $stmt = $this->connect()->prepare("INSERT INTO login (username, users_email, users_password) VALUES (?, ?, ?);");

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        if(!$stmt->execute(array($username, $email, $hashedPassword))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
    }

    protected function checkUser($username, $email) {
        $stmt = $this->connect()->prepare("SELECT username FROM login WHERE username = ? OR users_email = ?;");

        if(!$stmt->execute(array($username, $email))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }

        $resultCheck = true;
        if($stmt->rowCount() > 0) {
            $result = false;
        }

        return $resultCheck;
    }
}


