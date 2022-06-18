<?php

class SignupController extends Signup {

    private $username;
    private $email;
    private $new_password;
    private $confirm_password;

    public function __construct($username, $email, $new_password, $confirm_password){
        $this->username = $username;
        $this->email = $email;
        $this->new_password = $new_password;
        $this->confirm_password = $confirm_password;
    }

    public function signupUser() {
        if($this->emptyInput() == false) {
            // echo "Empty Input!";
            header("location: ../index.php?error=emptyinput");
            exit();
        }
        if($this->invalidUsername() == false) {
            //echo "Invalid Username!";
            header("location: ../index.php?error=username");
            exit();
        }
        if($this->invalidEmail() == false) {
            //echo "Invalid email!";
            header("location: ../index.php?error=email");
            exit();
        }
        if($this->passwordMatch() == false) {
            //echo "Password doesn't match!";
            header("location: ../index.php?error=password");
            exit();
        }
        if($this->userExists() == false) {
            //echo "Username already exists!";
            header("location: ../index.php?error=username");
            exit();
        }

        $this->setUser($this->username,$this->email,$this->new_password);
    }

    private function emptyInput() {
        $result = true;
        if(empty($this->username) || empty($this->email) || empty($this->new_password) || empty($this->confirm_password)){
            $result = false;
        }
        return $result;
    }

    private function invalidUsername() {
        $result = true;
        if(!preg_match("/^[a-zA-Z0-9]*$/", $this->username)) {
            $result = false;
        }
        return $result;
    }

    private function invalidEmail() {
        $result = true;
        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $result = false;
        }
        return $result;
    }

    private function passwordMatch() {
        $result = true;
        if($this->new_password !== $this->confirm_password) {
            $result = false;
        }
        return $result;
    }

    private function userExists() {
        $result = true;

        if(!$this->checkUser($this->username, $this->email)) {
            $result = false;
        }

        return $result;
    }


}

?>